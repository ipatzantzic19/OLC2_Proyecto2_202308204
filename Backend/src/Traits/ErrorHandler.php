<?php

namespace Golampi\Traits;

/**
 * Trait compartido entre el intérprete (P1) y el compilador (P2).
 *
 * Estandariza la captura de errores ANTLR4 en el formato
 * { type, description, line, column } usado por la GUI.
 */
trait ErrorHandler
{
    private array $_errors = [];

    /**
     * Añade un error ANTLR4 normalizado.
     * Se llama desde syntaxError() del ErrorListener.
     */
    protected function addAntlrError(
        $recognizer,
        string $msg,
        int $line,
        int $col
    ): void {
        $type = ($recognizer instanceof \Antlr\Antlr4\Runtime\Parser)
            ? 'Sintáctico'
            : 'Léxico';

        // Limpiar mensaje ANTLR (elimina "token recognition error at:" etc.)
        $desc = $msg;
        if (str_contains($msg, "token recognition error at:")) {
            $char = trim(str_replace("token recognition error at:", '', $msg));
            $desc = "Carácter no reconocido: $char";
        } elseif (str_contains($msg, "mismatched input")) {
            $desc = "Entrada incorrecta — $msg";
        } elseif (str_contains($msg, "missing")) {
            $desc = "Token faltante — $msg";
        } elseif (str_contains($msg, "extraneous input")) {
            $desc = "Token inesperado — $msg";
        }

        $this->_errors[] = [
            'type'        => $type,
            'description' => $desc,
            'line'        => $line,
            'column'      => $col,
        ];
    }

    public function getErrors(): array
    {
        return $this->_errors;
    }
}