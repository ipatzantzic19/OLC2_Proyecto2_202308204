<?php

namespace Golampi\Compiler\ARM64\Traits\FloatOps;

/**
 * FloatPool — Pool de constantes float32 en la sección .data
 *
 * Las constantes float32 no pueden cargarse directamente como inmediatos
 * en ARM64 (a diferencia de los enteros pequeños con mov #N).
 * En su lugar, se almacenan en la sección .data como valores .single (4 bytes)
 * y se cargan mediante direccionamiento PC-relativo (adrp + ldr).
 *
 * Esta es la misma estrategia que usa GCC para constantes flotantes.
 *
 * Deduplicación:
 *   El mismo valor float siempre obtiene el mismo label en .data.
 *   La clave de deduplicación son los bits IEEE-754 del valor,
 *   lo que garantiza que 1.5 y 1.5 comparten el mismo slot aunque
 *   aparezcan en distintas partes del código.
 *
 * Formato .data generado:
 *   .align 2
 *   .flt_0: .single 3.14
 *   .align 2
 *   .flt_1: .single 2.71828
 *
 * Carga en código:
 *   adrp x9, .flt_0              // x9 = página de .flt_0
 *   ldr  s0, [x9, :lo12:.flt_0]  // s0 = valor float32
 *
 * Estado que usa (definido en ARM64Generator):
 *   array $floatPool  → bits_ieee754 => label
 *   int   $floatIdx   → contador de constantes float
 */
trait FloatPool
{
    /**
     * Interna un valor float32 en la sección .data.
     * Si el valor ya existe, devuelve el label existente (deduplicación).
     *
     * @param  float  $val  Valor a internar
     * @return string       Label en .data (ej: ".flt_0")
     */
    protected function internFloat(float $val): string
    {
        // Representar los bits IEEE-754 como clave entera para deduplicación
        $bits = unpack('V', pack('f', $val))[1];

        if (!isset($this->floatPool[$bits])) {
            $label = '.flt_' . $this->floatIdx++;
            $this->floatPool[$bits] = $label;
            $this->addData('.align 2');
            $this->addData($label . ': .single ' . $this->floatToAsmLiteral($val));
        }

        return $this->floatPool[$bits];
    }

    /**
     * Convierte un float PHP a literal reconocido por GNU as.
     * Casos especiales: NaN e Infinito usan el formato hexadecimal de bits.
     * Asegura que los valores enteros tengan punto decimal (5.0 en lugar de 5).
     */
    private function floatToAsmLiteral(float $val): string
    {
        if (is_nan($val))      return '0r7FC00000'; // NaN canónico
        if (is_infinite($val)) return $val > 0 ? '0r7F800000' : '0rFF800000';
        $formatted = sprintf('%.10g', $val);
        // Asegurar que valores enteros tengan punto decimal (5.0 en lugar de 5)
        if (is_float($val) && floor($val) == $val && strpos($formatted, '.') === false) {
            return $formatted . '.0';
        }
        return $formatted;
    }
}