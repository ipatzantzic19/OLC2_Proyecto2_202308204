<?php

namespace Golampi\Compiler\ARM64\Traits\Emitter;

/**
 * InstructionEmitter — Emisión de instrucciones ARM64 individuales
 *
 * Provee los métodos de bajo nivel para escribir instrucciones,
 * etiquetas y comentarios en el buffer de la sección .text.
 *
 * Formato de línea ARM64 (GNU as):
 *   \t<mnemónico> <operandos>    // comentario opcional (alineado a columna 44)
 *
 * La alineación del comentario mejora la legibilidad del assembly generado
 * y es la práctica estándar en compiladores que producen código para humanos.
 *
 * Gestión del stack de temporales:
 *   pushStack() reserva 16 bytes (mantiene alineación de 16 que exige AArch64)
 *   y almacena x0 en [sp]. Se usa para guardar el resultado de la evaluación
 *   de un sub-árbol mientras se evalúa el otro operando de una expresión binaria.
 *
 * Estado que modifica (definido en ARM64Generator):
 *   array $textLines  → buffer de la sección .text
 *   int   $labelIdx   → contador de etiquetas únicas
 */
trait InstructionEmitter
{
    /** Emite una instrucción con comentario opcional alineado. */
    protected function emit(string $instr, string $comment = ''): void
    {
        $line = "\t" . $instr;
        if ($comment !== '') {
            $padding = max(1, 44 - strlen($line));
            $line   .= str_repeat(' ', $padding) . '// ' . $comment;
        }
        $this->textLines[] = $line;
    }

    /** Emite una etiqueta (label:) sin indentación. */
    protected function label(string $name): void
    {
        $this->textLines[] = $name . ':';
    }

    /** Emite un comentario indentado (// texto). */
    protected function comment(string $text): void
    {
        $this->textLines[] = "\t// " . $text;
    }

    /** Añade una línea cruda a la sección .data. */
    protected function addData(string $line): void
    {
        $this->dataLines[] = $line;
    }

    /**
     * Genera un label único con prefijo dado.
     * Los labels llevan '.' para indicar que son locales al archivo.
     */
    protected function newLabel(string $prefix = 'L'): string
    {
        return '.' . $prefix . '_' . ($this->labelIdx++);
    }

    /**
     * Apila x0 en el stack (alineación 16 bytes requerida por AArch64).
     * Patrón estándar para preservar un valor mientras se evalúa otro.
     */
    protected function pushStack(): void
    {
        $this->emit('sub sp, sp, #16', 'reservar slot temporal');
        $this->emit('str x0, [sp]',    'x0 → stack temporal');
        if ($this->func) $this->func->pushTemp();
    }

    /**
     * Emite la instrucción binaria correcta para (x1 OP x0 → x0).
     * Precondición: x1 = lhs (recuperado del stack), x0 = rhs.
     */
    protected function emitBinaryOp(string $op): void
    {
        match ($op) {
            '+'  => $this->emit('add x0, x1, x0',  'int32 suma'),
            '-'  => $this->emit('sub x0, x1, x0',  'int32 resta'),
            '*'  => $this->emit('mul x0, x1, x0',  'int32 mul'),
            '/'  => $this->emit('sdiv x0, x1, x0', 'int32 div (con signo)'),
            default => null,
        };
    }
}