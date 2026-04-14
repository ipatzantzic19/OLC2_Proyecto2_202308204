<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

/**
 * Transfer — Generación ARM64 para sentencias de transferencia de control
 *
 * FIX FASE 2: visitReturnStatement soporta multi-return (int32, bool).
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  CONVENCIÓN AArch64 PARA MULTI-RETURN (AAPCS64)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   Retorno simple:
 *     - int32 / bool / rune / string / pointer  → x0
 *     - float32                                 → s0
 *
 *   Retorno doble (Golampi — hasta 128 bits):
 *     - primer  valor int   → x0
 *     - segundo valor int   → x1
 *     - primer  valor float → s0
 *     - segundo valor float → s1   (o x0 si el primero era int)
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  ESTRATEGIA DE GENERACIÓN DE CÓDIGO MULTI-RETURN
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   return expr0, expr1
 *
 *   1. Evaluar expr0  → x0/s0
 *   2. push x0/s0 al stack temporal
 *   3. Evaluar expr1  → x0/s0
 *   4. push x0/s0 al stack temporal
 *   5. Cargar en registros de retorno (orden inverso del stack):
 *        stack[sp + n-1 * 16] = expr0  → x0 / s0
 *        stack[sp + n-2 * 16] = expr1  → x1 / (s1 o x1)
 *   6. Limpiar stack temporal
 *   7. b .epilogue_funcname
 *
 *   Invariante clave: cada visit() restaura sp antes de retornar,
 *   por lo que los offsets calculados son siempre correctos.
 *
 * Modelos de compiladores (Aho et al.):
 *   - break/continue resuelven la pila de contexto de bucles ($loopStack).
 *   - return salta al epílogo centralizado de la función ($func->epilogueLabel),
 *     evitando duplicar ldp/add/ret por cada punto de retorno.
 */
trait Transfer
{
    // ── break ─────────────────────────────────────────────────────────────

    public function visitBreakStatement($ctx)
    {
        if (empty($this->loopStack)) {
            $this->addError(
                'Semántico',
                "'break' fuera de bucle o switch",
                $ctx->getStart()->getLine(),
                $ctx->getStart()->getCharPositionInLine()
            );
            return null;
        }

        $breakLabel = end($this->loopStack)['break'];
        $this->emit("b $breakLabel", 'break → salida del bucle/switch');
        return null;
    }

    // ── continue ──────────────────────────────────────────────────────────

    public function visitContinueStatement($ctx)
    {
        if (empty($this->loopStack)) {
            $this->addError(
                'Semántico',
                "'continue' fuera de bucle",
                $ctx->getStart()->getLine(),
                $ctx->getStart()->getCharPositionInLine()
            );
            return null;
        }

        $continueLabel = end($this->loopStack)['continue'];
        $this->emit("b $continueLabel", 'continue → siguiente iteración');
        return null;
    }

    // ── return ────────────────────────────────────────────────────────────

    /**
     * Genera código de retorno de función.
     *
     * Casos soportados:
     *   return            → mov x0, #0  (vacío)
     *   return expr       → x0 o s0 según tipo
     *   return e0, e1     → x0 + x1    (multi-return enteros/bool)
     *   return e0, e1     → s0 + x0    (float + int)
     *
     * El salto al epílogo centraliza la restauración del frame, evitando
     * duplicar ldp x29,x30 + add sp + ret por cada punto de retorno.
     */
    public function visitReturnStatement($ctx)
    {
        $exprList = $ctx->expressionList();
        $epilogue = $this->func ? $this->func->epilogueLabel : '.epilogue_unknown';

        // ── Retorno vacío ─────────────────────────────────────────────────
        if ($exprList === null) {
            $this->emit('mov x0, #0', 'return vacío → nil/0');
            $this->emit("b $epilogue", 'return → epílogo');
            return null;
        }

        // Recolectar expresiones (los separadores ',' son TerminalNodes en índices impares)
        $exprs = [];
        for ($i = 0; $i < $exprList->getChildCount(); $i += 2) {
            $exprs[] = $exprList->getChild($i);
        }
        $n = count($exprs);

        // ── Retorno simple ────────────────────────────────────────────────
        if ($n === 1) {
            $this->comment('return — valor único');
            $this->visit($exprs[0]);
            // resultado ya en x0 o s0 según tipo; el epílogo no lo toca
            $this->emit("b $epilogue", 'return → epílogo');
            return null;
        }

        // ── Retorno múltiple ──────────────────────────────────────────────
        // Aho et al. — "generación de código para retornos múltiples":
        //   Se evalúa cada expresión y su resultado se apila temporalmente.
        //   Tras evaluar todas, se cargan en los registros de retorno
        //   respetando la convención AArch64 (x0+x1 para enteros, s0 para float).
        //
        // Invariante de stack de visitores: cada visit() restaura sp antes
        // de retornar, así que los offsets calculados aquí son siempre válidos.

        $this->comment("return múltiple — $n valores");

        // Paso 1: evaluar cada expresión y apilar resultado
        $types = [];
        foreach ($exprs as $expr) {
            $type    = $this->visit($expr) ?? 'int32';
            $types[] = $type;

            $this->emit('sub sp, sp, #16', 'slot temporal multi-return');
            if ($type === 'float32') {
                $this->emit('str s0, [sp]', 'apilar float retorno');
            } else {
                $this->emit('str x0, [sp]', 'apilar int retorno');
            }
        }

        // Layout del stack después de los pushes (sp = tope):
        //   sp + 0        → último expr apilado  (índice n-1)
        //   sp + 16       → penúltimo            (índice n-2)
        //   sp + (n-1)*16 → primer expr apilado  (índice 0)
        //
        // Fórmula: offset para expr[i] = (n-1-i) * 16

        // Paso 2: cargar en registros de retorno AArch64
        // Convención simplificada del proyecto:
        //   • valores enteros/bool  → x0, x1, x2, …
        //   • valores float32       → s0, s1, s2, …
        $intReg   = 0;
        $floatReg = 0;

        $this->comment('cargar registros de retorno desde stack');
        for ($i = 0; $i < $n; $i++) {
            $stackOffset = ($n - 1 - $i) * 16;

            if ($types[$i] === 'float32') {
                $reg = 's' . $floatReg++;
                $this->emit("ldr $reg, [sp, #$stackOffset]",
                    "retorno[$i] float → $reg");
            } else {
                $reg = 'x' . $intReg++;
                $this->emit("ldr $reg, [sp, #$stackOffset]",
                    "retorno[$i] {$types[$i]} → $reg");
            }
        }

        // Paso 3: limpiar stack temporal
        $stackBytes = $n * 16;
        $this->emit("add sp, sp, #$stackBytes", 'limpiar slots temporales');

        $this->emit("b $epilogue", 'return múltiple → epílogo');
        return null;
    }

    // ── Passthrough de bloque y sentencia expresión ───────────────────────

    /**
     * visitExpressionStatement: delega en la expresión.
     */
    public function visitExpressionStatement($ctx)
    {
        return $this->visit($ctx->expression());
    }

    /**
     * visitBlock: genera el bloque directamente.
     * Se usa cuando un bloque aparece como sentencia independiente.
     */
    public function visitBlock($ctx)
    {
        $this->generateBlock($ctx);
        return null;
    }
}