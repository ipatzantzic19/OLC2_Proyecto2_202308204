<?php

namespace Golampi\Compiler\ARM64\Traits\FloatOps;

/**
 * FloatComparison — Comparaciones y conversiones de tipo float32 en ARM64
 *
 * Implementa:
 *   - Comparaciones float32 mediante fcmp + cset
 *   - Conversiones bidireccionales int32 ↔ float32
 *   - Conversión float32 → float64 para llamadas variadic (printf)
 *
 * Instrucción fcmp en AArch64:
 *   fcmp s1, s0  → establece flags NZCV según la comparación de s1 y s0
 *                  (lhs en s1, rhs en s0, convención del compilador)
 *
 * Condiciones ARM para comparaciones float (difieren de las enteras):
 *   fcmp establece flags distintos a cmp para manejar NaN.
 *   Las condiciones 'mi' y 'ls' son específicas del resultado de fcmp:
 *     mi (minus)     → s1 < s0   para <
 *     ls (lower/same)→ s1 <= s0  para <=
 *
 * Conversiones de tipo (tabla de promoción, enunciado sección 3.3.6):
 *   scvtf s0, w0  → signed convert to float: int32 (w0) → float32 (s0)
 *   fcvtzs w0, s0 → float convert to zero (signed): float32 (s0) → int32 (w0)
 *   fcvt d0, s0   → float32 → float64 (para printf variadic que requiere double)
 */
trait FloatComparison
{
    /**
     * Emite comparación float y deja resultado bool (0/1) en x0.
     * Precondición: s1 = lhs, s0 = rhs (ya cargados via pushFloatStack/popFloatStack).
     */
    protected function emitFloatComparison(string $op): void
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
        $this->emit("cset x0, $cond", "bool resultado comparación float ($op)");
    }

    /**
     * Convierte int32 (w0) → float32 (s0).
     * scvtf = "signed convert to float"
     */
    protected function emitIntToFloat(): void
    {
        $this->emit('scvtf s0, w0', 'int32 → float32');
    }

    /**
     * Convierte float32 (s0) → int32 (x0), truncando hacia cero.
     * fcvtzs = "float convert to zero, signed"
     * sxtw extiende el resultado de 32 a 64 bits con signo.
     */
    protected function emitFloatToInt(): void
    {
        $this->emit('fcvtzs w0, s0', 'float32 → int32 (truncar hacia cero)');
        $this->emit('sxtw x0, w0',   'sign-extend 32→64 bits');
    }

    /**
     * Convierte float32 (s0) → float64 (d0).
     * Necesario para llamadas a printf que es variadic: AArch64 ABI
     * requiere que los argumentos float variadic se pasen como double (d0).
     * 
     * Para ARM64 AAPCS variadic: floats deben estar TANTO en d0 como en x1
     */
    protected function emitFloat32ToDouble(): void
    {
        $this->emit('fcvt d0, s0', 'float32 a float64 para printf variadic');
        $this->emit('fmov x1, d0', 'copiar float64 a x1 para variadic ABI');
    }

    /** Guarda s0 en el slot float del frame. */
    protected function storeFloat(int $offset): void
    {
        $this->emit("str s0, [x29, #-$offset]", 'float32 → stack frame');
    }

    /** Carga el slot float del frame en s0. */
    protected function loadFloat(int $offset): void
    {
        $this->emit("ldr s0, [x29, #-$offset]", 'stack frame → s0');
    }
}