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
     * 
     * OPTIMIZACIÓN: Si estamos en contexto de salto condicional dentro de if,
     * emitir un salto directo (b.gt, b.le, etc.) en lugar de cset + cbz.
     * 
     * Usa cmp + cset (normal) o cmp + b.cond (optimizado en contexto if).
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

        // OPTIMIZACIÓN: Si estamos en contexto de salto if, emitir salto directo
        $jumpCtx = $this->getConditionalJumpContext();
        if ($jumpCtx !== null) {
            // Estamos dentro de if: generar salto directo
            $cond = $this->resolveIntCondition($op);
            if ($jumpCtx['inverted']) {
                // Saltar si FALSO (invertir la condición)
                $cond = $this->invertCondition($cond);
            }
            $this->emit("b.$cond " . $jumpCtx['label'], "saltar si $op");
        } else {
            // Contexto normal: generar bool
            $cond = $this->resolveIntCondition($op);
            $this->emit("cset x0, $cond", "bool resultado ($op)");
        }
    }

    /**
     * Invierte una condición ARM64 para el salto opuesto.
     * eq ↔ ne, lt ↔ ge, le ↔ gt, etc.
     */
    private function invertCondition(string $cond): string
    {
        return match ($cond) {
            'eq' => 'ne',
            'ne' => 'eq',
            'lt' => 'ge',
            'le' => 'gt',
            'gt' => 'le',
            'ge' => 'lt',
            default => $cond,
        };
    }

    /**
     * Comparación para float32.
     * OPTIMIZACIÓN: También soporta saltos directos en contexto if.
     * Usa pushFloatStack + fcmp + cset o fcmp + b.cond.
     */
    private function generateFloatComparison(string $op, callable $evalRhs): void
    {
        // Guardar lhs (s0) en stack
        $this->pushFloatStack();
        // Evaluar rhs → s0
        $evalRhs();
        // Recuperar lhs en s1
        $this->popFloatStack();  // s1 = lhs, s0 = rhs

        // OPTIMIZACIÓN: Si estamos en contexto de salto, emitir salto float
        $jumpCtx = $this->getConditionalJumpContext();
        if ($jumpCtx !== null) {
            $this->emitFloatComparisonWithJump($op, $jumpCtx);
        } else {
            $this->emitFloatComparison($op);
        }
    }

    // ── Resolución de condiciones ─────────────────────────────────────────

    /**
     * Emite comparación float con salto directo en contexto de if.
     * Precondición: s1 = lhs, s0 = rhs (ya cargados).
     */
    private function emitFloatComparisonWithJump(string $op, array $jumpCtx): void
    {
        $this->emit('fcmp s1, s0', 'comparar floats (lhs s1 vs rhs s0)');
        
        $cond = match ($op) {
            '=='  => 'eq',
            '!='  => 'ne',
            '>'   => 'gt',
            '>='  => 'ge',
            '<'   => 'mi',  // ARM: minus = lhs < rhs en resultado de fcmp
            '<='  => 'ls',  // ARM: lower or same
            default => 'eq',
        };
        
        if ($jumpCtx['inverted']) {
            $cond = $this->invertFloatCondition($cond);
        }
        
        $this->emit("b.$cond " . $jumpCtx['label'], "saltar si $op (float)");
    }

    /**
     * Invierte una condición float ARM64.
     * Nota: float usa 'mi'/'ls' en lugar de 'lt'/'le'.
     */
    private function invertFloatCondition(string $cond): string
    {
        return match ($cond) {
            'eq' => 'ne',
            'ne' => 'eq',
            'lt' => 'ge',
            'le' => 'gt',
            'gt' => 'le',
            'ge' => 'lt',
            'mi' => 'pl',  // mi (minus) ↔ pl (plus/greater) para <
            'ls' => 'hi',  // ls (lower/same) ↔ hi (higher) para <=
            'hi' => 'ls',  // Inverso de hi
            'pl' => 'mi',  // Inverso de pl
            default => $cond,
        };
    }

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