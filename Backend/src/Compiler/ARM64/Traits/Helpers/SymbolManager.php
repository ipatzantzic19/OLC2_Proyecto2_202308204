<?php

namespace Golampi\Compiler\ARM64\Traits\Helpers;

/**
 * SymbolManager — Tabla de símbolos y reporte de errores del compilador
 *
 * Gestiona dos estructuras de datos del compilador:
 *
 *   Tabla de símbolos:
 *     Registra cada identificador declarado (variable, función, constante)
 *     con su tipo, ámbito, valor inicial (si aplica) y posición en el fuente.
 *     Es el resultado observable del análisis semántico (enunciado sección 3.5.2).
 *
 *   Tabla de errores:
 *     Acumula los errores detectados durante la compilación sin detener
 *     el proceso (recuperación de errores). Incluye errores léxicos,
 *     sintácticos y semánticos (enunciado sección 3.5.1).
 *
 * Formato de entrada en tabla de símbolos:
 *   { identifier, type, scope, value, line, column }
 *
 * Formato de entrada en tabla de errores:
 *   { type, description, line, column }
 */
trait SymbolManager
{
    /**
     * Registra un símbolo en la tabla de símbolos.
     * El valor puede ser null para funciones o variables sin inicializar.
     */
    protected function addSymbol(
        string $id,
        string $type,
        string $scope,
        $value,
        int $line,
        int $col
    ): void {
        $this->symbolTable[] = [
            'identifier' => $id,
            'type'       => $type,
            'scope'      => $scope,
            'value'      => $value,
            'line'       => $line,
            'column'     => $col,
        ];
    }

    /** Retorna la tabla de símbolos completa. */
    public function getSymbolTable(): array
    {
        return $this->symbolTable;
    }

    /**
     * Registra un error de compilación.
     * El tipo puede ser: 'Léxico', 'Sintáctico', 'Semántico', 'Fatal'.
     */
    protected function addError(string $type, string $desc, int $line, int $col): void
    {
        $this->errors[] = [
            'type'        => $type,
            'description' => $desc,
            'line'        => $line,
            'column'      => $col,
        ];
    }

    /** Retorna la tabla de errores completa. */
    public function getErrors(): array
    {
        return $this->errors;
    }
}