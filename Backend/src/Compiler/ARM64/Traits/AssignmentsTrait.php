<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * AssignmentsTrait — Fase 2
 *
 * Generación de código ARM64 para asignaciones.
 * Cambios Fase 2:
 *   - Distingue float32 vs int32 para ldr/str correcto (s0 vs x0)
 *   - Asignaciones compuestas (+=, -=, etc.) con float
 *   - ++ y -- solo para int32/rune (sin cambio)
 */
trait AssignmentsTrait
{
    // ─── x = expr  /  x op= expr ────────────────────────────────────────────

    public function visitSimpleAssignment($ctx)
    {
        $name = $ctx->ID()->getText();
        $op   = $ctx->assignOp()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            return null;
        }

        $offset   = $this->func->getOffset($name);
        $varType  = $this->func->getType($name);
        $isFloat  = ($varType === 'float32');

        if ($op === '=') {
            $this->comment("$name = expr");
            $exprType = $this->visit($ctx->expression());
            // Coerción automática si es necesario
            if ($isFloat && ($exprType === 'int32' || $exprType === 'rune')) {
                $this->emitIntToFloat();
            } elseif (!$isFloat && $exprType === 'float32') {
                $this->emitFloatToInt();
            }
            $this->storeToFrame($varType, $offset);

        } else {
            // Operadores compuestos: cargar lhs, evaluar rhs, aplicar op, guardar
            $this->comment("$name $op expr");

            if ($isFloat) {
                $this->loadFromFrame('float32', $offset);   // s0 = lhs
                $this->pushFloatStack();                     // push lhs
                $rhsType = $this->visit($ctx->expression());// rhs → s0
                if ($rhsType === 'int32' || $rhsType === 'rune') {
                    $this->emitIntToFloat();
                }
                $this->popFloatStack();                      // s1=lhs, s0=rhs
                $scalar = match ($op) {
                    '+=' => '+', '-=' => '-', '*=' => '*', '/=' => '/', default => '+'
                };
                $this->emitFloatBinaryOp($scalar);
                $this->storeToFrame('float32', $offset);
            } else {
                $this->loadFromFrame($varType, $offset);     // x0 = lhs
                $this->pushStack();
                $this->visit($ctx->expression());             // rhs → x0
                $this->emit('ldr x1, [sp]',                  'lhs ← stack');
                $this->emit('add sp, sp, #16');
                $scalar = match ($op) {
                    '+=' => '+', '-=' => '-', '*=' => '*', '/=' => '/', default => '+'
                };
                $this->emitBinaryOp($scalar);
                $this->storeToFrame($varType, $offset);
            }
        }
        return null;
    }

    public function visitArrayAssignment($ctx)   { return null; }  // Fase 3
    public function visitPointerAssignment($ctx) { return null; }  // Fase 3

    // ─── x++ ────────────────────────────────────────────────────────────────

    public function visitIncrementStatement($ctx)
    {
        $name = $ctx->ID()->getText();
        if (!$this->func || !$this->func->hasLocal($name)) return null;

        $offset = $this->func->getOffset($name);
        $this->comment("$name++");
        $this->emit("ldr x0, [x29, #-$offset]");
        $this->emit('add x0, x0, #1');
        $this->emit("str x0, [x29, #-$offset]");
        return null;
    }

    // ─── x-- ────────────────────────────────────────────────────────────────

    public function visitDecrementStatement($ctx)
    {
        $name = $ctx->ID()->getText();
        if (!$this->func || !$this->func->hasLocal($name)) return null;

        $offset = $this->func->getOffset($name);
        $this->comment("$name--");
        $this->emit("ldr x0, [x29, #-$offset]");
        $this->emit('sub x0, x0, #1');
        $this->emit("str x0, [x29, #-$offset]");
        return null;
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function storeToFrame(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("str s0, [x29, #-$offset]");
        } else {
            $this->emit("str x0, [x29, #-$offset]");
        }
    }

    private function loadFromFrame(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("ldr s0, [x29, #-$offset]");
        } else {
            $this->emit("ldr x0, [x29, #-$offset]");
        }
    }
}