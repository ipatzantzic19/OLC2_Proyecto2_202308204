<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

/**
 * TransferTrait — Generación ARM64 para sentencias de transferencia de control
 *
 * Implementa las tres sentencias de transferencia del lenguaje Golampi:
 *   - break     : sale del bucle o switch más cercano
 *   - continue  : salta al inicio de la siguiente iteración
 *   - return    : sale de la función actual con un valor opcional
 *
 * También provee los passthrough de visitBlock y visitExpressionStatement.
 *
 * Modelo de compiladores (Aho et al. — control flow):
 *
 *   break/continue usan la pila de contexto de bucles ($loopStack) para
 *   resolver el label de destino en tiempo de compilación. Cada vez que
 *   se entra en un bucle (for clásico, while, infinito) se empuja un par
 *   { 'break' => label, 'continue' => label } y se saca al salir.
 *
 *   return genera un salto al epílogo de la función actual ($func->epilogueLabel),
 *   que contiene la restauración del frame (add sp, ldp x29/x30, ret).
 *   Esto centraliza el epílogo y evita duplicar el código de restauración
 *   para cada punto de retorno (patrón estándar en compiladores reales).
 *
 * Validaciones semánticas:
 *   - break/continue fuera de bucle → error y no se emite el salto
 *   - return fuera de función → no puede ocurrir dado el diseño del visitor
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
     * Genera el código de retorno de función.
     *
     * Convención AArch64:
     *   - Retorno int32/bool/string/rune/pointer → valor en x0
     *   - Retorno float32 → valor en s0
     *   - Sin valor → x0 = 0 (nil)
     *   - Multi-retorno → x0 + x1 (hasta 128 bits)
     *
     * El salto al epílogo centraliza la restauración del frame,
     * evitando duplicar ldp x29,x30 + add sp + ret en cada return.
     */
    public function visitReturnStatement($ctx)
    {
        $exprList = $ctx->expressionList();

        if ($exprList !== null) {
            // Evaluar el primer valor de retorno → x0 o s0 según tipo
            $this->comment('return — evaluar valor de retorno');
            $this->visit($exprList->getChild(0));
        } else {
            // return vacío → retornar 0 (void en Go retorna nil)
            $this->emit('mov x0, #0', 'return vacío → nil');
        }

        // Saltar al epílogo de la función actual
        $epilogue = $this->func ? $this->func->epilogueLabel : '.epilogue_unknown';
        $this->emit("b $epilogue", 'return → epílogo de la función');
        return null;
    }

    // ── Passthrough de bloque y sentencia expresión ───────────────────────

    /**
     * visitExpressionStatement: delega en la expresión.
     * Necesario porque statement → expressionStatement → expression.
     */
    public function visitExpressionStatement($ctx)
    {
        return $this->visit($ctx->expression());
    }

    /**
     * visitBlock: genera el bloque directamente.
     * Se usa cuando un bloque aparece como sentencia independiente
     * (por ejemplo, bloques anónimos para acotar scope).
     */
    public function visitBlock($ctx)
    {
        $this->generateBlock($ctx);
        return null;
    }
}