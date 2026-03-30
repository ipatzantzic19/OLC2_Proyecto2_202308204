<?php

namespace Golampi\Compiler\ARM64\Traits\StringOps;

/**
 * NowHelper — Generación ARM64 para la función built-in now()
 *
 * Implementa la función embebida now() que retorna la fecha y hora
 * actual del sistema en formato "YYYY-MM-DD HH:MM:SS".
 *
 * Según el enunciado (sección 3.3.13):
 *   now() → string con fecha/hora actual del sistema
 *   Formato: YYYY-MM-DD HH:MM:SS
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  IMPLEMENTACIÓN ARM64
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * La función utiliza las siguientes llamadas a la libc POSIX:
 *
 *   1. time(NULL)     → obtiene tiempo Unix (segundos desde epoch) en x0
 *   2. localtime(&t)  → convierte a struct tm* con hora local
 *   3. strftime(buf, 20, fmt, tm*) → formatea según el patrón dado
 *
 * El resultado se almacena en un buffer estático golampi_now_buf[20]
 * en la sección .data. Dado que now() no se llama con alta frecuencia
 * en programas típicos, un buffer estático es suficiente y evita
 * la complejidad del manejo dinámico de memoria.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  NOTA SOBRE EL BUFFER ESTÁTICO
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * El buffer de 20 bytes es exactamente el tamaño requerido:
 *   "2026-03-28 17:30:00" = 19 caracteres + 1 byte nulo = 20 bytes
 *
 * Este buffer se sobrescribe en cada llamada a now(). Si se necesitan
 * múltiples valores de now() simultáneamente, deben copiarse a otras
 * variables (string := now(); str2 := now()).
 */
trait NowHelper
{
    /** Flag: evita emitir el helper más de una vez en el archivo .s */
    private bool $nowHelperEmitted = false;

    /**
     * Genera la llamada al helper golampi_now.
     * Retorna un puntero al buffer estático con la fecha formateada.
     */
    protected function emitNow(): void
    {
        $this->ensureNowHelper();
        $this->comment('now() → x0 = puntero a "YYYY-MM-DD HH:MM:SS"');
        $this->emit('bl golampi_now');
    }

    /**
     * Emite el helper golampi_now y el buffer estático en .data.
     *
     * Pipeline: time → localtime → strftime → return buffer
     *
     * Convención AArch64 para strftime:
     *   x0 = dest buffer
     *   x1 = size
     *   x2 = format string
     *   x3 = struct tm*
     */
    protected function ensureNowHelper(): void
    {
        if ($this->nowHelperEmitted) return;
        $this->nowHelperEmitted = true;

        // Buffer estático en .data para el resultado de strftime
        $this->addData('.align 3');
        $this->addData('golampi_now_buf: .space 20');

        // Internar el formato de fecha en el pool de strings
        $fmtLabel = $this->internString('%Y-%m-%d %H:%M:%S');

        $helper = <<<ASM

// ── golampi_now() → x0 = "YYYY-MM-DD HH:MM:SS" ───────────────────────────
// Pipeline: time(NULL) → localtime(&t) → strftime(buf, 20, fmt, tm*)
// Retorna puntero al buffer estático golampi_now_buf[20]
golampi_now:
    stp x29, x30, [sp, #-32]!
    mov x29, sp

    // time(NULL) → x0 = segundos desde epoch
    mov x0, #0
    bl time
    str x0, [x29, #-8]          // guardar time_t

    // localtime(&t) → x0 = struct tm* (tiempo local)
    sub x0, x29, #8             // dirección de t en el stack
    bl localtime
    mov x19, x0                 // x19 = tm* (callee-saved, sobrevive printf)

    // strftime(buf, 20, fmt, tm*)  →  formatea la fecha
    adrp x0, golampi_now_buf
    add  x0, x0, :lo12:golampi_now_buf
    mov  x1, #20
    adrp x2, $fmtLabel
    add  x2, x2, :lo12:$fmtLabel
    mov  x3, x19               // tm* como 4to argumento
    bl   strftime

    // retornar puntero al buffer con la fecha formateada
    adrp x0, golampi_now_buf
    add  x0, x0, :lo12:golampi_now_buf
    ldp x29, x30, [sp], #32
    ret
ASM;
        $this->postTextLines[] = $helper;
    }
}