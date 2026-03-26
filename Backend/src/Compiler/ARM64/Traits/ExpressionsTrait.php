<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * ExpressionsTrait — Fase 2
 *
 * Genera código ARM64 para expresiones con soporte completo de tipos:
 *   - int32 : resultado en x0
 *   - float32: resultado en s0
 *   - bool   : resultado en x0 (0 o 1)
 *   - string : resultado en x0 (puntero)
 *   - rune   : resultado en x0 (alias int32)
 *
 * Tabla de promoción de tipos (del enunciado):
 *   int32  + int32   = int32     (add x0, x1, x0)
 *   int32  + float32 = float32   (scvtf lhs, fadd)
 *   float32+ int32   = float32   (scvtf rhs, fadd)
 *   float32+ float32 = float32   (fadd s0, s1, s0)
 *   string + string  = string    (concat helper)
 *   rune   + int32   = int32     (add)
 *   rune   + rune    = int32     (add)
 *
 * Estrategia de temporales (Aho et al. — descriptores):
 *   Cada sub-expresión deja su resultado en x0 (int) o s0 (float).
 *   Para operadores binarios:
 *     1. Evaluar lhs → push al stack
 *     2. Evaluar rhs → x0 / s0
 *     3. pop lhs del stack → x1 / s1
 *     4. Emitir instrucción binaria → resultado en x0 / s0
 */
trait ExpressionsTrait
{
    // ─── Punto de entrada ────────────────────────────────────────────────────

    public function visitExpression($ctx)
    {
        return $this->visit($ctx->logicalOr());
    }

    // ─── OR lógico (cortocircuito) ───────────────────────────────────────────

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
        $this->emit('cset x0, ne',              'bool resultado OR');
        return 'bool';
    }

    // ─── AND lógico (cortocircuito) ──────────────────────────────────────────

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
        $this->emit('cset x0, ne',              'bool resultado AND');
        return 'bool';
    }

    // ─── Igualdad: == y != ───────────────────────────────────────────────────

    public function visitEquality($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->relational(0));
        }

        $lhsType = $this->visit($ctx->relational(0));
        $op      = $ctx->getChild(1)->getText();

        if ($lhsType === 'float32') {
            // float: guardar s0 en stack
            $this->pushFloatStack();
            $this->visit($ctx->relational(1));
            $this->popFloatStack();     // s1 = lhs, s0 = rhs
            $this->emitFloatComparison($op);
        } else {
            $this->pushStack();
            $this->visit($ctx->relational(1));
            $this->emit('ldr x1, [sp]');
            $this->emit('add sp, sp, #16');
            $this->emit('cmp x1, x0');
            $this->emit($op === '==' ? 'cset x0, eq' : 'cset x0, ne');
        }
        return 'bool';
    }

    // ─── Relacional: >, >=, <, <= ────────────────────────────────────────────

    public function visitRelational($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->additive(0));
        }

        $lhsType = $this->visit($ctx->additive(0));
        $op      = $ctx->getChild(1)->getText();

        if ($lhsType === 'float32') {
            $this->pushFloatStack();
            $this->visit($ctx->additive(1));
            $this->popFloatStack();
            $this->emitFloatComparison($op);
        } else {
            $this->pushStack();
            $this->visit($ctx->additive(1));
            $this->emit('ldr x1, [sp]');
            $this->emit('add sp, sp, #16');
            $this->emit('cmp x1, x0');
            $cond = match ($op) {
                '>'  => 'gt', '>=' => 'ge',
                '<'  => 'lt', '<=' => 'le',
                default => 'eq'
            };
            $this->emit("cset x0, $cond");
        }
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

            if ($type === 'float32') {
                $this->pushFloatStack();
                $rhsType = $this->visit($ctx->multiplicative($mIdx++));
                // Si rhs es int32, convertir a float
                if ($rhsType === 'int32' || $rhsType === 'rune') {
                    $this->emitIntToFloat();
                }
                $this->popFloatStack();   // s1=lhs, s0=rhs
                $this->emitFloatBinaryOp($op);

            } elseif ($type === 'int32' || $type === 'rune') {
                // Verificar si rhs es float → promover lhs
                $this->pushStack();
                $rhsType = $this->visit($ctx->multiplicative($mIdx++));

                if ($rhsType === 'float32') {
                    // Promover lhs int32 → float32
                    // lhs está en stack como bits, recuperar y convertir
                    $this->emit('ldr x1, [sp]');
                    $this->emit('add sp, sp, #16');
                    $this->emit('scvtf s1, w1',      'lhs int32 → float32');
                    // s0 ya tiene rhs float
                    $this->emitFloatBinaryOp($op);
                    $type = 'float32';
                } else {
                    $this->emit('ldr x1, [sp]');
                    $this->emit('add sp, sp, #16');
                    if ($op === '+') $this->emit('add x0, x1, x0');
                    else             $this->emit('sub x0, x1, x0');
                }

            } elseif ($type === 'string' && $op === '+') {
                // Concatenación de strings
                $this->pushStack();                    // push lhs ptr
                $this->visit($ctx->multiplicative($mIdx++));  // rhs en x0
                $this->emit('mov x1, x0',             'rhs string → x1');
                $this->emit('ldr x0, [sp]',           'lhs string ← stack → x0');
                $this->emit('add sp, sp, #16');
                $this->emitStringConcat();             // bl golampi_concat, resultado x0

            } else {
                // Tipo no soportado para esta operación
                $this->pushStack();
                $this->visit($ctx->multiplicative($mIdx++));
                $this->emit('ldr x1, [sp]');
                $this->emit('add sp, sp, #16');
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

            if ($type === 'float32') {
                $this->pushFloatStack();
                $rhsType = $this->visit($ctx->unary($uIdx++));
                if ($rhsType === 'int32' || $rhsType === 'rune') {
                    $this->emitIntToFloat();
                }
                $this->popFloatStack();
                $this->emitFloatBinaryOp($op);

            } else {
                $this->pushStack();
                $rhsType = $this->visit($ctx->unary($uIdx++));

                if ($rhsType === 'float32' && ($op === '*' || $op === '/')) {
                    // Promover lhs → float
                    $this->emit('ldr x1, [sp]');
                    $this->emit('add sp, sp, #16');
                    $this->emit('scvtf s1, w1',      'lhs int32 → float32');
                    $this->emitFloatBinaryOp($op);
                    $type = 'float32';
                } else {
                    $this->emit('ldr x1, [sp]');
                    $this->emit('add sp, sp, #16');
                    switch ($op) {
                        case '*': $this->emit('mul x0, x1, x0');  break;
                        case '/': $this->emit('sdiv x0, x1, x0'); break;
                        case '%':
                            $this->emit('sdiv x2, x1, x0',     'cociente');
                            $this->emit('msub x0, x2, x0, x1', 'resto = lhs - coc*rhs');
                            break;
                    }
                }
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
        if ($type === 'float32') {
            $this->emit('fneg s0, s0',   'negación float32');
        } else {
            $this->emit('neg x0, x0',    'negación int32');
        }
        return $type;
    }

    public function visitNotUnary($ctx)
    {
        $this->visit($ctx->unary());
        $this->emit('eor x0, x0, #1',   'NOT lógico');
        return 'bool';
    }

    // ─── Punteros ────────────────────────────────────────────────────────────

    public function visitAddressOf($ctx)
    {
        $name = $ctx->ID()->getText();
        if ($this->func && $this->func->hasLocal($name)) {
            $offset = $this->func->getOffset($name);
            $this->emit("sub x0, x29, #$offset",  "&$name → dirección en frame");
        } else {
            $this->addError('Semántico', "Variable '$name' no declarada", 0, 0);
            $this->emit('mov x0, xzr');
        }
        return 'pointer';
    }

    public function visitDereference($ctx)
    {
        $type = $this->visit($ctx->unary());
        $this->emit('ldr x0, [x0]',    '*ptr → valor');
        return 'int32';  // Fase 2: asumimos *T donde T=int32
    }

    // ─── Agrupación ──────────────────────────────────────────────────────────

    public function visitGroupedExpression($ctx)
    {
        return $this->visit($ctx->expression());
    }

    // ─── Arrays (Fase 3) ─────────────────────────────────────────────────────

    public function visitArrayAccess($ctx)
    {
        $this->emit('mov x0, xzr',  'array access — Fase 3');
        return 'int32';
    }

    public function visitArrayLiteralExpr($ctx)
    {
        $this->emit('mov x0, xzr',  'array literal — Fase 3');
        return 'array';
    }
}