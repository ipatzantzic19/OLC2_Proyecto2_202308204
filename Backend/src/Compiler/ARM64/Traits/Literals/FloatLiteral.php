<?php

namespace Golampi\Compiler\ARM64\Traits\Literals;

/**
 * FloatLiteral — Generación ARM64 para literales float32
 *
 * Carga un valor float32 literal en s0 mediante el pool de constantes
 * float en la sección .data.
 *
 * ARM64 no permite cargar constantes float directamente como inmediatos
 * de 32 bits arbitrarios (a diferencia de enteros). La solución estándar
 * (usada también por GCC) es almacenar el valor en .data y cargarlo con
 * direccionamiento PC-relativo:
 *
 *   .flt_0: .single 3.14          ← sección .data
 *
 *   adrp x9, .flt_0               ← cargar página base
 *   ldr  s0, [x9, :lo12:.flt_0]   ← cargar los 4 bytes IEEE-754
 *
 * x9 se usa como registro temporal de dirección (caller-saved, no
 * interfiere con x0-x7 que son los registros de resultado/argumentos).
 *
 * El pool deduplica valores iguales (ver FloatPool): dos literales 1.5
 * en el código comparten el mismo slot en .data.
 */
trait FloatLiteral
{
    public function visitFloatLiteral($ctx)
    {
        $text  = $ctx->FLOAT32()->getText();
        $val   = (float) $text;
        $label = $this->internFloat($val);  // internado en FloatPool

        // Capturar valor para la tabla de símbolos
        $this->lastLiteralValue = ['type' => 'float32', 'value' => $val];

        $this->comment("float32 literal $text → s0");
        $this->emit("adrp x9, $label",               'página de la constante float');
        $this->emit("ldr s0, [x9, :lo12:$label]",    'cargar float32 IEEE-754 en s0');
        return 'float32';
    }
}