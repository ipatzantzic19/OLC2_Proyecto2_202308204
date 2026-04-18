<?php

namespace Golampi\Compiler\ARM64\Traits\Literals;

/**
 * IntLiteral — Generación ARM64 para literales enteros int32
 *
 * Carga un valor entero literal en x0 usando la secuencia de instrucciones
 * más eficiente según el rango del valor.
 *
 * Estrategias de carga en AArch64:
 *
 *   0              → mov x0, xzr         (registro cero, sin inmediato)
 *   1–65535        → mov x0, #N          (inmediato de 16 bits)
 *   65536–2^31-1   → mov x0, #lo16 + movk x0, #hi16, lsl #16
 *   Negativos      → cargar abs + neg x0, x0
 *
 * ARM64 solo permite inmediatos de 16 bits en mov. Para valores mayores
 * se usa movk ("move with keep") que inserta 16 bits en una posición
 * específica del registro sin alterar los demás bits.
 */
trait IntLiteral
{
    public function visitIntLiteral($ctx)
    {
        $val = (int) $ctx->INT32()->getText();

        if ($val === 0) {
            $this->emit('mov x0, xzr', 'int32 literal 0 (64-bit per AArch64)');

        } elseif ($val > 0 && $val <= 65535) {
            $this->emit("mov x0, #$val", 'int32 literal (64-bit per AArch64)');

        } elseif ($val > 65535 && $val <= 0x7FFFFFFF) {
            // Para valores > 16 bits, usar x0 (64-bit) ya que movk requiere x
            $lo = $val & 0xFFFF;
            $hi = ($val >> 16) & 0xFFFF;
            $this->emit("mov x0, #$lo", 'int32 literal (lo bits)');
            if ($hi) $this->emit("movk x0, #$hi, lsl #16", 'int32 literal (hi bits)');

        } else {
            // Negativo: cargar valor absoluto y negar
            $abs = abs($val);
            $lo  = $abs & 0xFFFF;
            $hi  = ($abs >> 16) & 0xFFFF;
            $this->emit("mov x0, #$lo", 'int32 literal (negativo - lo)');
            if ($hi) $this->emit("movk x0, #$hi, lsl #16", 'int32 literal (negativo - hi)');
            if ($val < 0) $this->emit('neg x0, x0', "literal negativo $val (64-bit per AArch64)");
        }
        return 'int32';
    }
}