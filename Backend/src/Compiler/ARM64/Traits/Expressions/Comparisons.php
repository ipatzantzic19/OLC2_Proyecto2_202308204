<?php

namespace Golampi\Compiler\ARM64\Traits\Expressions;

/**
 * ComparisonsTrait — Generación ARM64 para operadores de comparación
 *
 * Implementa los operadores de comparación de Golampi:
 *
 *   Igualdad/Desigualdad: ==, !=
 *   Relacional:           >, >=, <, <=
 *
 * Tabla de compatibilidad de tipos (enunciado sección 3.3.7):
 *
 *   == / != : int32↔int32, int32↔float32, int32↔rune,
 *             float32↔float32, float32↔rune, rune↔rune,
 *             bool↔bool, string↔string
 *
 *   > >= < <=: int32↔int32, int32↔float32, int32↔rune,
 *              float32↔float32, float32↔rune, rune↔rune,
 *              string↔string
 *
 * Resultado: siempre bool (0 o 1 en x0).
 *
 * Estrategia de generación ARM64:
 *
 *   Para int32/bool/rune:
 *     [eval lhs] → push x0
 *     [eval rhs] → x0
 *     ldr x1, [sp]          // recuperar lhs
 *     add sp, sp, #16
 *     cmp x1, x0            // comparar lhs vs rhs
 *     cset x0, <cond>       // bool resultado en x0
 *
 *   Para float32:
 *     [eval lhs] → pushFloatStack (s0 → stack)
 *     [eval rhs] → s0
 *     ldr s1, [sp]          // recuperar lhs en s1
 *     add sp, sp, #16
 *     fcmp s1, s0           // comparar floats
 *     cset x0, <cond>       // bool resultado en x0
 *
 * Condiciones AArch64 para comparaciones float (fcmp):
 *   <  → 'mi'  (minus = lhs < rhs en resultado de fcmp)
 *   <= → 'ls'  (lower or same)
 *   >  → 'gt'
 *   >= → 'ge'
 *   == → 'eq'
 *   != → 'ne'
 */
trait Comparisons
{
    // ── Igualdad: == y != ─────────────────────────────────────────────────

    public function visitEquality($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->relational(0));
        }

        $lhsType = $this->visit($ctx->relational(0));
        $op      = $ctx->getChild(1)->getText();

        $this->generateComparison($lhsType, $op, function() use ($ctx) {
            return $this->visit($ctx->relational(1));
        });

        return 'bool';
    }

    // ── Relacional: >, >=, <, <= ──────────────────────────────────────────

    public function visitRelational($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->additive(0));
        }

        $lhsType = $this->visit($ctx->additive(0));
        $op      = $ctx->getChild(1)->getText();

        $this->generateComparison($lhsType, $op, function() use ($ctx) {
            return $this->visit($ctx->additive(1));
        });

        return 'bool';
    }

    // ── Generador de comparación ──────────────────────────────────────────

    /**
     * Genera el código de comparación según el tipo del operando izquierdo.
     *
     * @param string   $lhsType  Tipo del operando izquierdo (ya evaluado)
     * @param string   $op       Operador de comparación ('==', '!=', etc.)
     * @param callable $evalRhs  Closure que evalúa el operando derecho
     */
    private function generateComparison(string $lhsType, string $op, callable $evalRhs): void
    {
        if ($lhsType === 'float32') {
            $this->generateFloatComparison($op, $evalRhs);
        } else {
            $this->generateIntComparison($op, $evalRhs);
        }
    }

    /**
     * Comparación para tipos enteros (int32, bool, rune, string pointer).
     * Usa cmp + cset.
     */
    private function generateIntComparison(string $op, callable $evalRhs): void
    {
        // Guardar lhs en stack
        $this->pushStack();
        // Evaluar rhs → x0
        $evalRhs();
        // Recuperar lhs en x1
        $this->emit('ldr x1, [sp]', 'lhs ← stack');
        $this->emit('add sp, sp, #16');

        $this->emit('cmp x1, x0', 'comparar lhs vs rhs');
        $cond = $this->resolveIntCondition($op);
        $this->emit("cset x0, $cond", "bool resultado ($op)");
    }

    /**
     * Comparación para float32.
     * Usa pushFloatStack + fcmp + cset con condiciones especiales.
     */
    private function generateFloatComparison(string $op, callable $evalRhs): void
    {
        // Guardar lhs (s0) en stack
        $this->pushFloatStack();
        // Evaluar rhs → s0
        $evalRhs();
        // Recuperar lhs en s1
        $this->popFloatStack();  // s1 = lhs, s0 = rhs

        $this->emitFloatComparison($op);
    }

    // ── Resolución de condiciones ─────────────────────────────────────────

    /**
     * Mapea operador de comparación → condición AArch64 para enteros.
     */
    private function resolveIntCondition(string $op): string
    {
        return match ($op) {
            '=='  => 'eq',
            '!='  => 'ne',
            '>'   => 'gt',
            '>='  => 'ge',
            '<'   => 'lt',
            '<='  => 'le',
            default => 'eq',
        };
    }
}