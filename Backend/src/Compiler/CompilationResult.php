<?php

namespace Golampi\Compiler;

/**
 * DTO que encapsula el resultado completo de una compilación.
 * Contiene el código ensamblador generado, errores y tabla de símbolos.
 */
class CompilationResult
{
    public bool   $success;
    public string $assembly;      // Código ARM64 generado
    public array  $errors;        // [{type, description, line, column}]
    public array  $symbolTable;   // [{identifier, type, scope, value, line, column}]
    public string $executionTime;
    public string $timestamp;

    // Salida del programa (si se ejecutó con QEMU — Fase 4)
    public string $programOutput = '';

    public function __construct(
        string $assembly,
        array  $errors,
        array  $symbolTable,
        string $executionTime = '0ms'
    ) {
        $this->assembly      = $assembly;
        $this->errors        = $errors;
        $this->symbolTable   = $symbolTable;
        $this->success       = empty($errors);
        $this->executionTime = $executionTime;
        $this->timestamp     = date('Y-m-d H:i:s');
    }

    /** Serializa para la respuesta JSON de la API */
    public function toArray(): array
    {
        return [
            'success'       => $this->success,
            'assembly'      => $this->assembly,
            'errors'        => $this->errors,
            'symbolTable'   => $this->symbolTable,
            'programOutput' => $this->programOutput,
            'executionTime' => $this->executionTime,
            'timestamp'     => $this->timestamp,
            'errorCount'    => count($this->errors),
            'symbolCount'   => count($this->symbolTable),
        ];
    }
}