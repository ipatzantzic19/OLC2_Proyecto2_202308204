<?php

namespace Golampi\Compiler\ARM64\Traits\StringOps;

/**
 * SubstrHelper — Generación ARM64 para la función built-in substr
 *
 * Implementa la función embebida substr(s, inicio, longitud) que extrae
 * una subcadena a partir de una cadena dada.
 *
 * Según el enunciado (sección 3.3.13), substr:
 *   - Extrae una subcadena indicando el índice inicial y la longitud
 *   - Genera error si los índices son inválidos
 *   - Retorna el nuevo string como un puntero en x0
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  ALGORITMO DE IMPLEMENTACIÓN
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   dest = malloc(length + 1)     // +1 para el byte nulo
 *   memcpy(dest, s + start, length)
 *   dest[length] = '\0'           // null terminator
 *   return dest
 *
 * La función memcpy copia exactamente `length` bytes desde la posición
 * `s + start` al buffer `dest`. El null terminator se agrega manualmente
 * con strb wzr, [dest + length].
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  REGISTRO DE ACTIVACIÓN DE golampi_substr
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   Convención de entrada:
 *     x0 = puntero al string fuente
 *     x1 = índice de inicio (int32)
 *     x2 = longitud deseada (int32)
 *
 *   Convención de retorno:
 *     x0 = puntero al nuevo string en heap
 *
 * El helper se emite una sola vez al final del assembly.
 */
trait SubstrHelper
{
    /** Flag: evita emitir el helper más de una vez en el archivo .s */
    private bool $substrHelperEmitted = false;

    /**
     * Genera la llamada al helper golampi_substr.
     *
     * Precondición:
     *   x0 = puntero al string fuente
     *   x1 = índice de inicio
     *   x2 = longitud
     */
    protected function emitSubstr(): void
    {
        $this->ensureSubstrHelper();
        $this->comment('substr(x0=s, x1=start, x2=len) → x0 = substring en heap');
        $this->emit('bl golampi_substr');
    }

    /**
     * Emite el helper golampi_substr en el archivo .s (una sola vez).
     *
     * Registro de activación:
     *   [fp - 8]  = s      (string fuente)
     *   [fp - 16] = start  (índice inicio)
     *   [fp - 24] = len    (longitud)
     *   [fp - 32] = dest   (string resultado en heap)
     */
    protected function ensureSubstrHelper(): void
    {
        if ($this->substrHelperEmitted) return;
        $this->substrHelperEmitted = true;

        $helper = <<<'ASM'

// ── golampi_substr(x0=s, x1=start, x2=len) → x0 = substring en heap ──────
// Implementa: dest = malloc(len+1); memcpy(dest, s+start, len); dest[len]='\0'
// Registro de activación (48 bytes):
//   [fp - 8]  = s (fuente), [fp - 16] = start, [fp - 24] = len, [fp - 32] = dest
golampi_substr:
    stp x29, x30, [sp, #-48]!
    mov x29, sp
    str x0, [x29, #-8]          // guardar s
    str x1, [x29, #-16]         // guardar start
    str x2, [x29, #-24]         // guardar len

    // dest = malloc(len + 1)
    add x0, x2, #1
    bl malloc
    str x0, [x29, #-32]         // guardar dest

    // src = s + start  (calcular dirección del carácter inicial)
    ldr x1, [x29, #-8]          // x1 = s
    ldr x2, [x29, #-16]         // x2 = start
    add x1, x1, x2              // x1 = s + start

    // memcpy(dest, src, len)
    ldr x2, [x29, #-24]         // x2 = len
    bl memcpy

    // dest[len] = '\0'  (agregar null terminator)
    ldr x0, [x29, #-32]         // x0 = dest
    ldr x1, [x29, #-24]         // x1 = len
    add x1, x0, x1              // x1 = dest + len
    strb wzr, [x1]              // byte nulo al final

    // retornar dest
    ldr x0, [x29, #-32]
    ldp x29, x30, [sp], #48
    ret
ASM;
        $this->postTextLines[] = $helper;
    }
}