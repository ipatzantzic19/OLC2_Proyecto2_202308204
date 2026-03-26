<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * EmitterTrait — Fase 2
 *
 * Escribe instrucciones ARM64, gestiona etiquetas y comentarios,
 * y construye el string final de ensamblador.
 *
 * Cambios Fase 2:
 *   - $postTextLines: buffer para helpers (golampi_concat, golampi_substr, etc.)
 *     que se emiten AL FINAL del assembly, después de todas las funciones.
 *   - buildAssembly(): incluye los helpers del postTextLines y el pool de floats.
 *
 * Estado que usa de la clase:
 *   array  $dataLines
 *   array  $textLines
 *   array  $postTextLines   ← NUEVO
 *   int    $labelIdx
 */
trait EmitterTrait
{
    /** Buffer de líneas de texto que se emiten al final (helpers runtime). */
    protected array $postTextLines = [];

    // ── Escritura de instrucciones ────────────────────────────────────────────

    protected function emit(string $instr, string $comment = ''): void
    {
        $line = "\t" . $instr;
        if ($comment !== '') {
            $padding = max(1, 44 - strlen($line));
            $line .= str_repeat(' ', $padding) . '// ' . $comment;
        }
        $this->textLines[] = $line;
    }

    protected function label(string $name): void
    {
        $this->textLines[] = $name . ':';
    }

    protected function comment(string $text): void
    {
        $this->textLines[] = "\t// " . $text;
    }

    protected function addData(string $line): void
    {
        $this->dataLines[] = $line;
    }

    protected function newLabel(string $prefix = 'L'): string
    {
        return '.' . $prefix . '_' . ($this->labelIdx++);
    }

    // ── Helpers de stack / operaciones binarias ───────────────────────────────

    /** Apila x0 al stack (alineación 16). */
    protected function pushStack(): void
    {
        $this->emit('sub sp, sp, #16');
        $this->emit('str x0, [sp]');
        if ($this->func) $this->func->pushTemp();
    }

    /**
     * Emite la instrucción binaria correcta para (x1 OP x0 → x0).
     * Precondición: x1 = lhs, x0 = rhs.
     */
    protected function emitBinaryOp(string $op): void
    {
        match ($op) {
            '+'  => $this->emit('add x0, x1, x0'),
            '-'  => $this->emit('sub x0, x1, x0'),
            '*'  => $this->emit('mul x0, x1, x0'),
            '/'  => $this->emit('sdiv x0, x1, x0'),
            default => null,
        };
    }

    // ── Construcción del ensamblador final ────────────────────────────────────

    /**
     * Ensambla las secciones .data y .text en el string ARM64 final.
     *
     * Estructura del archivo .s generado:
     *   1. Cabecera con instrucciones de uso
     *   2. .arch armv8-a
     *   3. Sección .data (strings, floats, buffers)
     *   4. Sección .text (código de funciones)
     *   5. Helpers de runtime (golampi_concat, etc.) ← postTextLines
     */
    protected function buildAssembly(): string
    {
        $lines   = [];

        // ── Cabecera ──────────────────────────────────────────────────────────
        $lines[] = '// ============================================================';
        $lines[] = '// Golampi Compiler — Fase 2 — ARM64 (AArch64)';
        $lines[] = '// Compilar:';
        $lines[] = '//   aarch64-linux-gnu-gcc -o programa program.s -lc';
        $lines[] = '// Ejecutar:';
        $lines[] = '//   qemu-aarch64 -L /usr/aarch64-linux-gnu ./programa';
        $lines[] = '// ============================================================';
        $lines[] = '.arch armv8-a';
        $lines[] = '';

        // ── Sección .data (strings, floats, buffers) ──────────────────────────
        if (!empty($this->dataLines)) {
            $lines[] = '.section __DATA,__data';
            $lines[] = '.section .data';
            foreach ($this->dataLines as $l) {
                $lines[] = $l;
            }
            $lines[] = '';
        }

        // ── Sección .text (funciones compiladas) ──────────────────────────────
        $lines[] = '.section .text';
        $lines[] = '.global main';
        $lines[] = '';
        foreach ($this->textLines as $l) {
            $lines[] = $l;
        }

        // ── Helpers de runtime (golampi_concat, substr, now) ─────────────────
        if (!empty($this->postTextLines)) {
            $lines[] = '';
            $lines[] = '// ── Runtime helpers Golampi ─────────────────────────────────';
            foreach ($this->postTextLines as $block) {
                // Cada bloque ya viene con sus saltos de línea internos
                foreach (explode("\n", $block) as $line) {
                    $lines[] = $line;
                }
            }
        }

        return implode("\n", $lines);
    }
}