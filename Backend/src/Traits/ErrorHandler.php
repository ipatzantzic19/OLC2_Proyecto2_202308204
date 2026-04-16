<?php

namespace Golampi\Traits;

/**
 * ErrorHandler - Trait mínimo para gestión de errores ANTLR
 * 
 * Utilizado por CompilationHandler para registrar errores léxicos y sintácticos
 */
trait ErrorHandler
{
    protected array $errors = [];

    /**
     * Registra un error detectado por ANTLR (léxico o sintáctico)
     */
    public function addAntlrError(
        \Antlr\Antlr4\Runtime\Recognizer $recognizer,
        string $msg,
        int $line,
        int $charPositionInLine
    ): void {
        $this->errors[] = [
            'type'        => 'Sintáctico',
            'description' => $msg,
            'line'        => $line,
            'column'      => $charPositionInLine + 1,
        ];
    }

    /**
     * Retorna todos los errores registrados
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
