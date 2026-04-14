<?php

namespace Golampi\Compiler\ARM64\Traits\Emitter;

/**
 * AssemblyBuilder — Construcción del archivo ensamblador ARM64 final
 *
 * Responsable de ensamblar todas las secciones generadas durante la
 * compilación en un único string que representa el archivo .s válido
 * para el ensamblador GNU (gas) y compatible con aarch64-linux-gnu-gcc.
 *
 * Estructura del archivo .s generado:
 *
 *   // Cabecera (instrucciones de compilación y ejecución)
 *   .arch armv8-a
 *
 *   .section .data          ← strings, floats, buffers estáticos
 *     .str_0: .string "..."
 *     .flt_0: .single 3.14
 *     ...
 *
 *   .section .text
 *   .global main            ← punto de entrada del programa
 *
 *     main:                 ← código de funciones
 *       [instrucciones]
 *
 *     otra_funcion:
 *       [instrucciones]
 *
 *   // Runtime helpers      ← golampi_concat, golampi_substr, golampi_now
 *     golampi_concat:
 *       [instrucciones]
 *
 * Decisión de diseño:
 *   Los helpers de runtime (golampi_concat, etc.) se emiten DESPUÉS de todas
 *   las funciones de usuario para mantener main y las funciones principales
 *   al inicio del segmento de texto, lo que mejora la localidad de caché.
 *
 * Estado que lee (definido en ARM64Generator):
 *   array $dataLines     → líneas de la sección .data
 *   array $textLines     → líneas de la sección .text
 *   array $postTextLines → helpers de runtime
 */
trait AssemblyBuilder
{
    /**
     * Construye el string completo del archivo .s a partir de los buffers.
     * Se llama una única vez al finalizar la visita del árbol.
     */
    protected function buildAssembly(): string
    {
        $lines = [];

        // ── Cabecera ──────────────────────────────────────────────────────
        $lines[] = '# ============================================================';
        $lines[] = '# Golampi Compiler — Fase 2 — ARM64 (AArch64)';
        $lines[] = '# Compilar:';
        $lines[] = '#   aarch64-linux-gnu-gcc -o programa program.s -lc';
        $lines[] = '# Ejecutar:';
        $lines[] = '#   qemu-aarch64 -L /usr/aarch64-linux-gnu ./programa';
        $lines[] = '# ============================================================';
        $lines[] = '';

        // ── Sección .data ─────────────────────────────────────────────────
        if (!empty($this->dataLines)) {
            $lines[] = '.section .data';
            foreach ($this->dataLines as $l) {
                $lines[] = $l;
            }
            $lines[] = '';
        }

        // ── Sección .text ─────────────────────────────────────────────────
        $lines[] = '.section .text';
        $lines[] = '.global main';
        $lines[] = '';
        foreach ($this->textLines as $l) {
            $lines[] = $l;
        }

        // ── Helpers de runtime ────────────────────────────────────────────
        if (!empty($this->postTextLines)) {
            $lines[] = '';
            $lines[] = '# ── Runtime helpers Golampi ─────────────────────────────────';
            foreach ($this->postTextLines as $block) {
                foreach (explode("\n", $block) as $line) {
                    $lines[] = $line;
                }
            }
        }

        return implode("\n", $lines);
    }
}