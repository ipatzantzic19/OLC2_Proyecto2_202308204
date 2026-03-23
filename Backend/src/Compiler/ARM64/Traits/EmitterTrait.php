<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * EmitterTrait
 *
 * Responsabilidad: escribir instrucciones ARM64 en el buffer de texto,
 * manejar etiquetas y comentarios, y construir el string final de ensamblador.
 *
 * Estado que usa de la clase:
 *   array  $dataLines
 *   array  $textLines
 *   int    $labelIdx
 */
trait EmitterTrait
{
    // ── Escritura de instrucciones ────────────────────────────────────────────

    /** Emite una instrucción indentada con comentario opcional. */
    protected function emit(string $instr, string $comment = ''): void
    {
        $line = "\t" . $instr;
        if ($comment !== '') {
            $line .= str_repeat(' ', max(1, 40 - strlen($line))) . '// ' . $comment;
        }
        $this->textLines[] = $line;
    }

    /** Emite una etiqueta (sin indentación). */
    protected function label(string $name): void
    {
        $this->textLines[] = $name . ':';
    }

    /** Emite una línea de comentario. */
    protected function comment(string $text): void
    {
        $this->textLines[] = "\t// " . $text;
    }

    /** Añade una línea a la sección .data. */
    protected function addData(string $line): void
    {
        $this->dataLines[] = $line;
    }

    /** Genera una etiqueta única con el prefijo dado. */
    protected function newLabel(string $prefix = 'L'): string
    {
        return '.' . $prefix . '_' . ($this->labelIdx++);
    }

    // ── Helpers de stack / operaciones binarias ───────────────────────────────

    /** Apila x0 al stack (mantiene alineación de 16 bytes). */
    protected function pushStack(): void
    {
        $this->emit('sub sp, sp, #16');
        $this->emit('str x0, [sp]');
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
     * Ensambla las secciones .data y .text en el string final ARM64.
     */
    protected function buildAssembly(): string
    {
        $lines   = [];
        $lines[] = '// ============================================================';
        $lines[] = '// Golampi Compiler — Fase 1 — ARM64 (AArch64)';
        $lines[] = '// Compilar: aarch64-linux-gnu-gcc -o prog program.s';
        $lines[] = '// Ejecutar: qemu-aarch64 -L /usr/aarch64-linux-gnu ./prog';
        $lines[] = '// ============================================================';
        $lines[] = '.arch armv8-a';
        $lines[] = '';

        if (!empty($this->dataLines)) {
            // macOS usa __DATA, Linux usa .data — generamos la directiva portable
            $lines[] = '.section __DATA,__data';
            $lines[] = '.section .data';
            foreach ($this->dataLines as $l) {
                $lines[] = $l;
            }
            $lines[] = '';
        }

        $lines[] = '.section .text';
        $lines[] = '.global main';
        $lines[] = '';

        foreach ($this->textLines as $l) {
            $lines[] = $l;
        }

        return implode("\n", $lines);
    }
}