<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * ExpressionsTrait
 *
 * Responsabilidad: generar código ARM64 para expresiones.
 * Todas las expresiones dejan su resultado en x0 y devuelven el tipo PHP string.
 *
 * Estrategia para operadores binarios:
 *   1. Evaluar lhs → x0
 *   2. pushStack()  → apila x0 (lhs)
 *   3. Evaluar rhs → x0
 *   4. ldr x1, [sp] → recupera lhs en x1
 *   5. add sp, sp, #16
 *   6. emitBinaryOp(op) → resultado en x0
 */
trait ExpressionsTrait
{
    // ─── Punto de entrada ────────────────────────────────────────────────────

    public function visitExpression($ctx)
    {
        return $this->visit($ctx->logicalOr());
    }

    // ─── Lógica: || (con cortocircuito) ──────────────────────────────────────

    public function visitLogicalOr($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->logicalAnd(0));
        }

        $endLabel = $this->newLabel('or_end');
        $this->visit($ctx->logicalAnd(0));
        $this->emit("cbnz x0, $endLabel",       'cortocircuito OR: si true → fin');

        $aIdx = 1;
        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $this->visit($ctx->logicalAnd($aIdx++));
            if ($i < $ctx->getChildCount() - 2) {
                $this->emit("cbnz x0, $endLabel");
            }
        }

        $this->label($endLabel);
        $this->emit('cmp x0, #0');
        $this->emit('cset x0, ne');
        return 'bool';
    }

    // ─── Lógica: && (con cortocircuito) ──────────────────────────────────────

    public function visitLogicalAnd($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->equality(0));
        }

        $endLabel = $this->newLabel('and_end');
        $this->visit($ctx->equality(0));
        $this->emit("cbz x0, $endLabel",        'cortocircuito AND: si false → fin');

        $eIdx = 1;
        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $this->visit($ctx->equality($eIdx++));
            if ($i < $ctx->getChildCount() - 2) {
                $this->emit("cbz x0, $endLabel");
            }
        }

        $this->label($endLabel);
        $this->emit('cmp x0, #0');
        $this->emit('cset x0, ne');
        return 'bool';
    }

    // ─── Igualdad: == y != ───────────────────────────────────────────────────

    public function visitEquality($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->relational(0));
        }

        $this->visit($ctx->relational(0));
        $this->pushStack();

        $op = $ctx->getChild(1)->getText();
        $this->visit($ctx->relational(1));
        $this->emit('ldr x1, [sp]');
        $this->emit('add sp, sp, #16');
        $this->emit('cmp x1, x0');
        $this->emit($op === '==' ? 'cset x0, eq' : 'cset x0, ne');
        return 'bool';
    }

    // ─── Relacional: >, >=, <, <= ────────────────────────────────────────────

    public function visitRelational($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->additive(0));
        }

        $this->visit($ctx->additive(0));
        $this->pushStack();

        $op = $ctx->getChild(1)->getText();
        $this->visit($ctx->additive(1));
        $this->emit('ldr x1, [sp]');   // x1 = lhs
        $this->emit('add sp, sp, #16');
        $this->emit('cmp x1, x0');     // cmp lhs, rhs

        $cond = match ($op) {
            '>'  => 'gt', '>=' => 'ge',
            '<'  => 'lt', '<=' => 'le',
            default => 'eq'
        };
        $this->emit("cset x0, $cond");
        return 'bool';
    }

    // ─── Aditivo: + y - ──────────────────────────────────────────────────────

    public function visitAdditive($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->multiplicative(0));
        }

        $type = $this->visit($ctx->multiplicative(0));
        $mIdx = 1;

        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $op = $ctx->getChild($i)->getText();
            $this->pushStack();
            $this->visit($ctx->multiplicative($mIdx++));
            $this->emit('ldr x1, [sp]');
            $this->emit('add sp, sp, #16');

            if ($type === 'string' && $op === '+') {
                // Concatenación → Fase 2
                $this->addError('Semántico', 'Concatenación de strings: disponible en Fase 2', 0, 0);
            } else {
                $this->emitBinaryOp($op);
            }
        }
        return $type;
    }

    // ─── Multiplicativo: *, /, % ─────────────────────────────────────────────

    public function visitMultiplicative($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->unary(0));
        }

        $type = $this->visit($ctx->unary(0));
        $uIdx = 1;

        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $op = $ctx->getChild($i)->getText();
            $this->pushStack();
            $this->visit($ctx->unary($uIdx++));
            $this->emit('ldr x1, [sp]');
            $this->emit('add sp, sp, #16');

            switch ($op) {
                case '*': $this->emit('mul x0, x1, x0');  break;
                case '/': $this->emit('sdiv x0, x1, x0'); break;
                case '%':
                    // x0 = x1 % x0  →  x0 = x1 - (x1 / x0) * x0
                    $this->emit('sdiv x2, x1, x0');
                    $this->emit('msub x0, x2, x0, x1');
                    break;
            }
        }
        return $type;
    }

    // ─── Unarios ─────────────────────────────────────────────────────────────

    public function visitPrimaryUnary($ctx)
    {
        return $this->visit($ctx->primary());
    }

    public function visitNegativeUnary($ctx)
    {
        $type = $this->visit($ctx->unary());
        $this->emit('neg x0, x0');
        return $type;
    }

    public function visitNotUnary($ctx)
    {
        $this->visit($ctx->unary());
        $this->emit('eor x0, x0, #1',  'NOT lógico');
        return 'bool';
    }

    // ─── Punteros (Fase 3) ───────────────────────────────────────────────────

    public function visitAddressOf($ctx)
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

    public function visitDereference($ctx)
    {
        $this->visit($ctx->unary());
        $this->emit('ldr x0, [x0]',  '*deref');
        return 'int32';
    }

    // ─── Agrupación ──────────────────────────────────────────────────────────

    public function visitGroupedExpression($ctx)
    {
        return $this->visit($ctx->expression());
    }

    // ─── Arrays (Fase 3) ─────────────────────────────────────────────────────

    public function visitArrayAccess($ctx)
    {
        $this->emit('mov x0, #0');
        return 'int32';
    }

    public function visitArrayLiteralExpr($ctx)
    {
        $this->emit('mov x0, #0');
        return 'array';
    }
}