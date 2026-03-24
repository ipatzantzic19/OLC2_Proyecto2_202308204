<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * FunctionCallTrait
 *
 * Responsabilidad: generar código ARM64 para llamadas a funciones:
 *   - Funciones internas: fmt.Println
 *   - Funciones de usuario declaradas en el programa
 *
 * Estrategia de impresión (fmt.Println):
 *   Una llamada a printf por argumento (simplificación Fase 1).
 *   Fase 2 consolidará en una sola llamada usando registro d0 para float.
 */
trait FunctionCallTrait
{
    // ─── Visitor principal ────────────────────────────────────────────────────

    public function visitFunctionCall($ctx)
    {
        $ids  = $ctx->ID();
        $name = is_array($ids)
            ? ($ids[0]->getText() . (count($ids) >= 2 ? '.' . $ids[1]->getText() : ''))
            : $ids->getText();

        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        // ── Funciones internas ──────────────────────────────────────────
        if ($name === 'fmt.Println' || $name === 'println') {
            $this->generateFmtPrintln($ctx->argumentList());
            return 'nil';
        }

        // ── Función de usuario ─────────────────────────────────────────
        if (isset($this->userFunctions[$name])) {
            return $this->generateUserCall($name, $ctx->argumentList());
        }

        $this->addError('Semántico', "Función '$name' no definida", $line, $col);
        $this->emit('mov x0, #0');
        return 'nil';
    }

    /**
     * Genera una llamada bl a una función de usuario.
     * Fase 1: soporta 0 ó 1 argumento en x0.
     * Fase 2 implementará el paso completo por x0–x7.
     */
    private function generateUserCall(string $name, $argListCtx): string
    {
        $args = [];
        if ($argListCtx) {
            for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
                $args[] = $argListCtx->getChild($i);
            }
        }

        $this->comment("llamada a $name");

        if (count($args) === 0) {
            $this->emit("bl $name");
        } elseif (count($args) === 1) {
            $argCtx = $args[0];
            $exprNode = null;
            try {
                if (is_callable([$argCtx, 'expression'])) {
                    $exprNode = $argCtx->expression();
                }
            } catch (\Throwable $e) {
                // Ignorar
            }
            $this->visit($exprNode ?? $argCtx);
            // x0 ya tiene el argumento
            $this->emit("bl $name");
        } else {
            // Multi-args: Fase 2
            // Por ahora: evaluar solo el primero y llamar
            $argCtx = $args[0];
            $exprNode = null;
            try {
                if (is_callable([$argCtx, 'expression'])) {
                    $exprNode = $argCtx->expression();
                }
            } catch (\Throwable $e) {
                // Ignorar
            }
            $this->visit($exprNode ?? $argCtx);
            $this->emit("bl $name",  '(multi-args: simplificado — Fase 2)');
        }

        return 'int32';
    }

    // ─── Argumentos ───────────────────────────────────────────────────────────

    public function visitExpressionArgument($ctx)
    {
        return $this->visit($ctx->expression());
    }

    public function visitAddressArgument($ctx)
    {
        $name = $ctx->ID()->getText();
        if ($this->func && $this->func->hasLocal($name)) {
            $offset = $this->func->getOffset($name);
            $this->emit("sub x0, x29, #$offset",  "&$name");
        } else {
            $this->emit('mov x0, #0');
        }
        return 'pointer';
    }

    // ─── fmt.Println ──────────────────────────────────────────────────────────

    /**
     * Genera una serie de llamadas a printf para implementar fmt.Println.
     * Estrategia Fase 1: una llamada printf por argumento + newline al final.
     */
    protected function generateFmtPrintln($argListCtx): void
    {
        if ($argListCtx === null) {
            // fmt.Println() → solo imprime un newline
            $nlLabel = $this->internString("\n");
            $this->emit("adrp x0, $nlLabel");
            $this->emit("add x0, x0, :lo12:$nlLabel");
            $this->emit('bl printf');
            return;
        }

        // Recolectar argumentos
        $argCtxList = [];
        for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
            $argCtxList[] = $argListCtx->getChild($i);
        }

        $n = count($argCtxList);

        for ($i = 0; $i < $n; $i++) {
            $isLast = ($i === $n - 1);
            $argCtx = $argCtxList[$i];

            // Evaluar argumento → x0
            $exprCtx = null;
            try {
                if (is_callable([$argCtx, 'expression'])) {
                    $exprCtx = $argCtx->expression();
                }
            } catch (\Throwable $e) {
                // Ignorar
            }
            $exprCtx = $exprCtx ?? $argCtx;

            $type = $exprCtx ? ($this->visit($exprCtx) ?? 'int32') : 'int32';

            $this->comment("fmt.Println arg $i ($type)");
            $this->generatePrintValue($type, $isLast ? '' : ' ');
        }

        // Newline final (comportamiento estándar de Println en Go)
        $nlLabel = $this->internString("\n");
        $this->emit("adrp x0, $nlLabel");
        $this->emit("add x0, x0, :lo12:$nlLabel");
        $this->emit('bl printf');
    }

    /**
     * Genera código printf para el valor en x0 según su tipo.
     *
     * @param string $type   Tipo del valor en x0
     * @param string $suffix '' para último arg, ' ' para intermedios
     */
    protected function generatePrintValue(string $type, string $suffix): void
    {
        switch ($type) {
            case 'int32':
            case 'rune':
                $fmt = $this->internString('%ld' . $suffix);
                $this->emit('mov x1, x0',               'entero → x1 para printf');
                $this->emit("adrp x0, $fmt");
                $this->emit("add x0, x0, :lo12:$fmt");
                $this->emit('bl printf');
                break;

            case 'float32':
                // Fase 1: bits de double ya en x0, mover a d0 para printf
                $fmt = $this->internString('%f' . $suffix);
                $this->emit('fmov d0, x0',              'bits float → d0');
                $this->emit("adrp x0, $fmt");
                $this->emit("add x0, x0, :lo12:$fmt");
                $this->emit('bl printf');
                break;

            case 'bool':
                $trueLabel  = $this->newLabel('bt');
                $doneLabel  = $this->newLabel('bd');
                $falseStr   = $this->internString('false' . $suffix);
                $trueStr    = $this->internString('true'  . $suffix);

                $this->emit("cbnz x0, $trueLabel");
                $this->emit("adrp x0, $falseStr");
                $this->emit("add x0, x0, :lo12:$falseStr");
                $this->emit('bl printf');
                $this->emit("b $doneLabel");
                $this->label($trueLabel);
                $this->emit("adrp x0, $trueStr");
                $this->emit("add x0, x0, :lo12:$trueStr");
                $this->emit('bl printf');
                $this->label($doneLabel);
                break;

            case 'string':
            default:
                $fmt = $this->internString('%s' . $suffix);
                $this->emit('mov x1, x0',               'ptr string → x1 para printf');
                $this->emit("adrp x0, $fmt");
                $this->emit("add x0, x0, :lo12:$fmt");
                $this->emit('bl printf');
                break;
        }
    }
}