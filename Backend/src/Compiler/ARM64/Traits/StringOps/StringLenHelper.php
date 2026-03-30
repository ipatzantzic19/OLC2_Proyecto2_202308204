<?php

namespace Golampi\Compiler\ARM64\Traits\StringOps;

/**
 * StringLenHelper — Generación ARM64 para len(string)
 *
 * Implementa la función built-in len() que retorna la longitud
 * de una cadena usando strlen de la libc estándar.
 *
 * Según el enunciado (sección 3.3.13):
 *   len(s) → entero con número de caracteres en s
 *   Usa strlen internamente: obtiene longitud hasta null terminator
 *
 * Convención de llamada:
 *   x0 = puntero al string
 *   Retorna x0 = longitud (entero de 32 bits, extendido a 64)
 */
trait StringLenHelper
{
    /**
     * Genera código para len(string).
     * Precondición: puntero a la cadena en x0.
     * Resultado: longitud (entero) en x0.
     */
    protected function emitStrlen(): void
    {
        $this->comment('len(string) → strlen(x0) → x0');
        $this->emit('bl strlen');
    }
}
