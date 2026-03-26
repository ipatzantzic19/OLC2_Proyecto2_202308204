<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * FloatOpsTrait — Fase 2
 *
 * Operaciones aritméticas y de comparación para float32 en ARM64.
 *
 * Convención de registros float:
 *   s0        → resultado de expresión float32 (equivalente a x0 para int)
 *   s1        → operando temporal izquierdo
 *   s8–s15    → caller-saved (disponibles en expresiones)
 *   s16–s31   → callee-saved (no se usan en Fase 2 salvo que sea necesario)
 *
 * Estrategia de pila para operaciones binarias float:
 *   1. Evaluar lhs → s0
 *   2. pushFloatStack()  → guarda s0 en stack (sub sp + str s0)
 *   3. Evaluar rhs → s0
 *   4. ldr s1, [sp]      → recupera lhs en s1
 *   5. add sp, sp, #16
 *   6. f<op> s0, s1, s0  → resultado en s0
 *
 * Almacenamiento en stack frame:
 *   Variables float32 usan 8 bytes de slot igual que int32 (alineación AArch64).
 *   Instrucción de store: str s0, [x29, #-offset]
 *   Instrucción de load:  ldr s0, [x29, #-offset]
 *
 * Pool de constantes float:
 *   Cada literal float32 se guarda en .data como .single (4 bytes).
 *   internFloat(val) → label en .data
 */
trait FloatOpsTrait
{
    /** Pool de constantes float: bits_as_int => label */
    protected array $floatPool = [];
    protected int   $floatIdx  = 0;

    // ═══════════════════════════════════════════════════════════════════════════
    //  POOL DE CONSTANTES FLOAT
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Interna un valor float32 en la sección .data como .single.
     * Deduplica: el mismo valor siempre obtiene el mismo label.
     *
     * @return string label (ej: ".flt_0")
     */
    protected function internFloat(float $val): string
    {
        // Usar los bits IEEE-754 como clave de deduplicación
        $bits = unpack('V', pack('f', $val))[1];

        if (!isset($this->floatPool[$bits])) {
            $label = '.flt_' . $this->floatIdx++;
            $this->floatPool[$bits] = $label;
            // .single emite exactamente 4 bytes IEEE-754 float32
            $this->addData(".align 2");
            $this->addData($label . ': .single ' . $this->floatToAsmLiteral($val));
        }
        return $this->floatPool[$bits];
    }

    /**
     * Convierte un float PHP a literal reconocido por GNU as.
     * GNU as acepta notación decimal: 3.14159, 0.5, -1.0
     * Para NaN/Inf usa valores especiales.
     */
    private function floatToAsmLiteral(float $val): string
    {
        if (is_nan($val))   return '0r7FC00000';  // NaN canonical
        if (is_infinite($val)) return $val > 0 ? '0r7F800000' : '0rFF800000';
        // Usar sprintf para máxima precisión sin notación científica cuando posible
        return sprintf('%.10g', $val);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  PUSH/POP FLOAT EN STACK
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Apila s0 en el stack (mantiene alineación de 16 bytes).
     * AArch64: str s0 usa 4 bytes pero el slot debe ser de 8 por alineación.
     */
    protected function pushFloatStack(): void
    {
        $this->emit('sub sp, sp, #16',      'push float32 (alineación 16)');
        $this->emit('str s0, [sp]',          's0 → stack');
        if ($this->func) $this->func->pushTemp();
    }

    /**
     * Recupera el float del stack en s1 y libera el slot.
     */
    protected function popFloatStack(): void
    {
        $this->emit('ldr s1, [sp]',          'stack → s1 (lhs float)');
        $this->emit('add sp, sp, #16');
        if ($this->func) $this->func->popTemp();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  OPERACIONES ARITMÉTICAS FLOAT
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Emite la instrucción aritmética float correcta.
     * Precondición: s1 = lhs, s0 = rhs → resultado en s0.
     */
    protected function emitFloatBinaryOp(string $op): void
    {
        match ($op) {
            '+'  => $this->emit('fadd s0, s1, s0',  'float32 suma'),
            '-'  => $this->emit('fsub s0, s1, s0',  'float32 resta'),
            '*'  => $this->emit('fmul s0, s1, s0',  'float32 mul'),
            '/'  => $this->emit('fdiv s0, s1, s0',  'float32 div'),
            default => null,
        };
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  COMPARACIONES FLOAT
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Emite comparación float y deja resultado bool en x0.
     * Precondición: s1 = lhs, s0 = rhs.
     *
     * AArch64: fcmp establece flags NZCV, luego cset lee las condiciones.
     * Las condiciones float usan sufijo 'f': gt, ge, lt, le, eq, ne.
     * Atención: 'mi' (minus) es '<' para floats sin signo comparado.
     */
    protected function emitFloatComparison(string $op): void
    {
        $this->emit('fcmp s1, s0',           'comparar floats (lhs vs rhs)');
        $cond = match ($op) {
            '=='  => 'eq',
            '!='  => 'ne',
            '>'   => 'gt',
            '>='  => 'ge',
            '<'   => 'mi',   // ARM: 'mi' = minus = lhs < rhs para fcmp
            '<='  => 'ls',   // ARM: 'ls' = less or same
            default => 'eq',
        };
        $this->emit("cset x0, $cond",       "bool resultado comparación float");
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  CONVERSIONES INT ↔ FLOAT
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Convierte x0 (int32) → s0 (float32).
     * scvtf: signed convert to float
     */
    protected function emitIntToFloat(): void
    {
        $this->emit('scvtf s0, w0',          'int32 → float32');
    }

    /**
     * Convierte s0 (float32) → x0 (int32, truncando hacia cero).
     * fcvtzs: float convert to zero (signed)
     */
    protected function emitFloatToInt(): void
    {
        $this->emit('fcvtzs w0, s0',         'float32 → int32 (truncar)');
        $this->emit('sxtw x0, w0',           'sign-extend 32→64');
    }

    /**
     * Convierte s0 (float32) → d0 (double/float64) para llamadas variadic.
     * printf("%f", ...) espera un double en AArch64 ABI.
     */
    protected function emitFloat32ToDouble(): void
    {
        $this->emit('fcvt d0, s0',           'float32 → float64 para printf');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  LOAD/STORE FLOAT EN FRAME
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Guarda s0 en el slot de la variable float en el frame.
     */
    protected function storeFloat(int $offset): void
    {
        $this->emit("str s0, [x29, #-$offset]",  'float32 → stack frame');
    }

    /**
     * Carga el valor float de una variable del frame en s0.
     */
    protected function loadFloat(int $offset): void
    {
        $this->emit("ldr s0, [x29, #-$offset]",  'stack frame → s0');
    }

    /**
     * Mueve s0 → x0 preservando los bits (para pasar float como argumento int).
     * Útil cuando una función genérica necesita el valor como entero.
     */
    protected function emitFloatBitsToInt(): void
    {
        $this->emit('fmov w0, s0',           'float32 bits → w0');
        $this->emit('sxtw x0, w0',           'sign-extend');
    }

    /**
     * Mueve x0 → s0 (interpretando bits como float).
     */
    protected function emitIntBitsToFloat(): void
    {
        $this->emit('fmov s0, w0',           'w0 bits → s0 float32');
    }
}