<?php

namespace Golampi\Compiler\ARM64\Traits\FloatOps;

/**
 * FloatArithmetic — Operaciones aritméticas ARM64 sobre registros SIMD float32
 *
 * Implementa las operaciones aritméticas para el tipo float32 usando
 * los registros SIMD/FP de AArch64 (s0–s31).
 *
 * Convención de registros float32 en este compilador:
 *   s0       → resultado de expresión float32 (análogo a x0 para int)
 *   s1       → operando temporal izquierdo en operaciones binarias
 *   s8–s15   → caller-saved (disponibles durante expresiones)
 *
 * Estrategia de pila para operaciones binarias float:
 *   1. eval(lhs)      → s0
 *   2. pushFloatStack → s0 al stack (sub sp, #16; str s0, [sp])
 *   3. eval(rhs)      → s0
 *   4. popFloatStack  → ldr s1, [sp]; add sp, #16
 *   5. f<op> s0, s1, s0
 *
 * Nota AArch64: str s0 almacena 4 bytes pero el slot ocupa 16 bytes
 * para mantener la alineación de 16 bytes que exige AArch64.
 *
 * Instrucciones generadas:
 *   fadd s0, s1, s0   → suma float32
 *   fsub s0, s1, s0   → resta float32
 *   fmul s0, s1, s0   → multiplicación float32
 *   fdiv s0, s1, s0   → división float32
 *   fneg s0, s0       - negacion float32 (unario)
 */
trait FloatArithmetic
{
    /** Apila s0 en el stack preservando la alineación de 16 bytes. */
    protected function pushFloatStack(): void
    {
        $this->emit('sub sp, sp, #16', 'reservar slot float temporal');
        $this->emit('str s0, [sp]',    's0 → stack temporal');
        if ($this->func) $this->func->pushTemp();
    }

    /**
     * Recupera el float del stack en s1 y libera el slot.
     * Postcondición: s1 = lhs (guardado), s0 = rhs (ya evaluado).
     */
    protected function popFloatStack(): void
    {
        $this->emit('ldr s1, [sp]', 'stack → s1 (lhs float)');
        $this->emit('add sp, sp, #16');
        if ($this->func) $this->func->popTemp();
    }

    /**
     * Emite la instrucción aritmética float correcta.
     * Precondición: s1 = lhs, s0 = rhs → resultado en s0.
     */
    protected function emitFloatBinaryOp(string $op): void
    {
        match ($op) {
            '+'  => $this->emit('fadd s0, s1, s0', 'float32 suma'),
            '-'  => $this->emit('fsub s0, s1, s0', 'float32 resta'),
            '*'  => $this->emit('fmul s0, s1, s0', 'float32 multiplicación'),
            '/'  => $this->emit('fdiv s0, s1, s0', 'float32 división'),
            default => null,
        };
    }
}