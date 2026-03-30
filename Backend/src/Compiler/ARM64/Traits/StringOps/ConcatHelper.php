<?php

namespace Golampi\Compiler\ARM64\Traits\StringOps;

/**
 * ConcatHelper — Generación ARM64 para concatenación de strings
 *
 * Implementa el operador `+` sobre strings y el helper de runtime
 * golampi_concat, que realiza la concatenación dinámica en el heap
 * usando las funciones de la libc estándar.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  MODELO DE MEMORIA PARA STRINGS (Aho et al.)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Los strings en Golampi son punteros a C-strings null-terminated en memoria.
 * La concatenación requiere:
 *   1. Calcular longitud total: strlen(a) + strlen(b) + 1
 *   2. Reservar memoria en el heap: malloc(total)
 *   3. Copiar primer string: strcpy(dest, a)
 *   4. Concatenar segundo: strcat(dest, b)
 *   5. Retornar puntero al nuevo string
 *
 * Los strings literales viven en la sección .data (read-only).
 * Los strings resultado de operaciones viven en el heap (malloc).
 * Esta distinción es importante para no intentar modificar .data.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  REGISTRO DE ACTIVACIÓN DE golampi_concat
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   [fp + 0]   → x29 (saved FP)
 *   [fp + 8]   → x30 (saved LR)
 *   [fp - 8]   → puntero a string 'a'
 *   [fp - 16]  → puntero a string 'b'
 *   [fp - 24]  → len_a (resultado de strlen(a))
 *   [fp - 32]  → len_b (resultado de strlen(b))
 *   [fp - 40]  → dest  (puntero al string resultado en heap)
 *
 * Convención de llamada:
 *   x0 = puntero a string 'a' (izquierdo)
 *   x1 = puntero a string 'b' (derecho)
 *   Retorna x0 = puntero al string concatenado (heap)
 *
 * Importante: el helper se emite una sola vez al final del assembly
 * (sección postTextLines), independientemente de cuántas concatenaciones
 * haya en el programa.
 */
trait ConcatHelper
{
    /** Flag: evita emitir el helper más de una vez en el archivo .s */
    private bool $concatHelperEmitted = false;

    /**
     * Genera el código para concatenar dos strings con golampi_concat.
     *
     * Precondición al llamar:
     *   - x0 = puntero al string izquierdo (lhs)
     *   - x1 = puntero al string derecho  (rhs)
     *
     * El helper golampi_concat es el único que sabe cómo manejar
     * la memoria dinámica para el resultado.
     */
    protected function emitStringConcat(): void
    {
        $this->ensureConcatHelper();
        $this->comment('string concatenación → golampi_concat(x0=lhs, x1=rhs)');
        $this->emit('bl golampi_concat', 'concat → x0 = string en heap');
    }

    /**
     * Garantiza que el helper ARM64 golampi_concat esté definido
     * en la sección de helpers del archivo .s.
     *
     * El helper implementa la siguiente lógica en ensamblador:
     *   dest = malloc(strlen(a) + strlen(b) + 1)
     *   strcpy(dest, a)
     *   strcat(dest, b)
     *   return dest
     *
     * Se emite en postTextLines para aparecer después de todas las
     * funciones de usuario, manteniendo main y las funciones principales
     * al inicio del segmento .text (mejor localidad de caché).
     */
    protected function ensureConcatHelper(): void
    {
        if ($this->concatHelperEmitted) return;
        $this->concatHelperEmitted = true;

        $helper = <<<'ASM'

// ── golampi_concat(x0=a, x1=b) → x0 = string concatenado en heap ─────────
// Registro de activación (48 bytes totales):
//   [fp + 0/8] = x29/x30 guardados
//   [fp - 8]   = ptr a
//   [fp - 16]  = ptr b
//   [fp - 24]  = len_a
//   [fp - 32]  = len_b
//   [fp - 40]  = dest (ptr resultado)
golampi_concat:
    stp x29, x30, [sp, #-48]!
    mov x29, sp
    str x0, [x29, #-8]          // guardar ptr a
    str x1, [x29, #-16]         // guardar ptr b

    // len_a = strlen(a)
    bl strlen
    str x0, [x29, #-24]         // len_a

    // len_b = strlen(b)
    ldr x0, [x29, #-16]
    bl strlen
    str x0, [x29, #-32]         // len_b

    // total = len_a + len_b + 1 (byte nulo final)
    ldr x1, [x29, #-24]
    add x0, x0, x1
    add x0, x0, #1

    // dest = malloc(total)
    bl malloc
    str x0, [x29, #-40]         // guardar dest

    // strcpy(dest, a)  →  dest = a
    ldr x1, [x29, #-8]
    bl strcpy

    // strcat(dest, b)  →  dest += b
    ldr x0, [x29, #-40]
    ldr x1, [x29, #-16]
    bl strcat

    // retornar dest en x0
    ldr x0, [x29, #-40]
    ldp x29, x30, [sp], #48
    ret
ASM;
        $this->postTextLines[] = $helper;
    }
}