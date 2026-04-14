<?php

namespace Golampi\Compiler\ARM64\Traits\StringOps;

/**
 * TypeOfHelper — Generación ARM64 para typeOf(expr)
 *
 * Implementa la función built-in typeOf() que retorna el tipo
 * de una expresión como una cadena constante (ej: "int32", "string").
 *
 * Según el enunciado:
 *   typeOf(expr) → string con el nombre del tipo
 *   Tipos: "int32", "float32", "bool", "string", "rune" (alias de int32), "nil"
 *
 * Implementación en compile-time:
 *   El tipo es conocido en el análisis semántico.
 *   Se genera un label constante en .data con el nombre,
 *   y se carga su dirección en x0.
 *
 * Convención de retorno:
 *   x0 = puntero a string constante en .data
 */
trait TypeOfHelper
{
    /**
     * Genera código para typeOf(expr) - string del tipo en x0.
     *
     * @param string $type tipo ya conocido en compile-time
     */
    protected function emitTypeOf(string $type): void
    {
        // Mapeo de tipos internos a nombres visibles (igual que en Go)
        $displayName = match ($type) {
            'int32'   => 'int32',
            'float32' => 'float32',
            'bool'    => 'bool',
            'string'  => 'string',
            'rune'    => 'int32',   // rune es alias de int32
            'nil'     => 'nil',
            default   => $type,
        };

        $label = $this->internString($displayName);
        $this->comment("typeOf: '$displayName'");
        $this->emit("adrp x0, $label");
        $this->emit("add x0, x0, :lo12:$label");
    }
}
