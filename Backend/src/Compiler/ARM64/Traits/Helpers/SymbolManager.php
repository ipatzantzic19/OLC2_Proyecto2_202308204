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
 *     Soporta gestión jerárquica de scopes para lenguajes con bloques anidados.
 *
 *   Tabla de errores:
 *     Acumula los errores detectados durante la compilación sin detener
 *     el proceso (recuperación de errores). Incluye errores léxicos,
 *     sintácticos y semánticos (enunciado sección 3.5.1).
 *
 * Formato de entrada en tabla de símbolos:
 *   { identifier, type, scope, value, line, column, isConstant, order }
 *
 * Formato de entrada en tabla de errores:
 *   { type, description, line, column }
 */
trait SymbolManager
{
    protected array $symbolTable = [];
    protected int $currentScope = 0;
    protected array $scopeStack = [];
    private int $declarationOrder = 0;

    /**
     * Entra en un nuevo scope (función, bloque, ciclo, etc.)
     */
    protected function enterScope(string $scopeName): void
    {
        $this->currentScope++;
        $this->scopeStack[] = [
            'id'      => $this->currentScope,
            'name'    => $scopeName,
            'symbols' => []
        ];
    }

    /**
     * Sale del scope actual y fusiona sus símbolos al padre o a la tabla global.
     * Los scopes de función vacían sus símbolos directamente a $this->symbolTable.
     * Los scopes de bloque/ciclo se fusionan con el scope padre.
     */
    protected function exitScope(): void
    {
        if (empty($this->scopeStack)) {
            return;
        }

        $scope     = array_pop($this->scopeStack);
        $scopeName = $scope['name'];
        $isFunctionScope = str_starts_with($scopeName, 'function:');

        foreach ($scope['symbols'] as $symbol) {
            if ($isFunctionScope || empty($this->scopeStack)) {
                $this->mergeIntoArray($this->symbolTable, $symbol);
            } else {
                $parentIdx = count($this->scopeStack) - 1;
                $this->mergeIntoArray($this->scopeStack[$parentIdx]['symbols'], $symbol);
            }
        }
    }

    /**
     * Fusiona un símbolo en un array destino.
     * Si existe la misma declaración (id + scope + línea + columna), actualiza el valor.
     * Si no existe, agrega como nueva entrada.
     */
    private function mergeIntoArray(array &$target, array $symbol): void
    {
        foreach ($target as &$existing) {
            if ($existing['identifier'] === $symbol['identifier']
                && $existing['scope']      === $symbol['scope']
                && $existing['line']       === $symbol['line']
                && $existing['column']     === $symbol['column']
            ) {
                $existing['value'] = $symbol['value'];
                return;
            }
        }
        unset($existing);
        $target[] = $symbol;
    }

    /**
     * Registra un símbolo en la tabla de símbolos.
     * Evita duplicados en el scope actual usando identifier como clave.
     * 
     * Si $value es null e tiene un lastLiteralValue pendiente,
     * usa ese valor (para capturar valores iniciales de literales).
     */
    protected function addSymbol(
        string $id,
        string $type,
        string $scope,
        $value = null,
        int $line = 0,
        int $col = 0,
        bool $isConstant = false
    ): bool {
        // No agregar símbolos vacíos
        if (empty($id) || $id === 'param') {
            return false;
        }

        // Si no existe en scope actual, agregarlo
        if ($this->symbolExistsInCurrentScope($id)) {
            return false;
        }

        // Mapear "_start" a "main" para reportes semánticos
        if ($scope === '_start') {
            $scope = 'main';
        }

        // Si no se proporciona valor, intentar usar lastLiteralValue si es una expresión literal
        if ($value === null && isset($this->lastLiteralValue) && $this->lastLiteralValue !== null) {
            if ($this->lastLiteralValue['type'] === $type) {
                $value = $this->lastLiteralValue['value'];
            }
            // Resetear después de usarlo
            $this->lastLiteralValue = null;
        }

        // Calcular valor por defecto si sigue siendo null y no es función
        if ($value === null && $type !== 'function') {
            $value = $this->getDefaultValue($type);
        } elseif ($type === 'function') {
            $value = null;
        }

        $symbol = [
            'identifier' => $id,
            'type'       => $type,
            'scope'      => $scope,
            'value'      => $value,
            'line'       => $line,
            'column'     => $col,
            'isConstant' => $isConstant,
            'order'      => $this->declarationOrder++
        ];

        if (!empty($this->scopeStack)) {
            $this->scopeStack[count($this->scopeStack) - 1]['symbols'][] = $symbol;
        } else {
            $this->symbolTable[] = $symbol;
        }

        return true;
    }

    /**
     * Actualiza el valor de un símbolo existente.
     * Busca desde el scope actual hacia afuera.
     */
    protected function updateSymbolValue(string $identifier, $newValue): bool
    {
        // Si el nuevo valor no es un puntero, no sobrescribir entradas cuyo
        // tipo ES un puntero (*T). Esto evita que una asignación via puntero
        // (*p = val) corrompa el registro del parámetro puntero.
        $newIsPointer = is_array($newValue) && isset($newValue['type']) && str_starts_with($newValue['type'], '*');

        // Buscar en scopeStack desde el más reciente
        for ($i = count($this->scopeStack) - 1; $i >= 0; $i--) {
            $symbols = &$this->scopeStack[$i]['symbols'];
            for ($j = 0; $j < count($symbols); $j++) {
                if ($symbols[$j]['identifier'] !== $identifier) {
                    continue;
                }
                $symIsPointer = str_starts_with($symbols[$j]['type'], '*');
                if ($symIsPointer && !$newIsPointer) {
                    continue;
                }
                $symbols[$j]['value'] = $newValue;
                return true;
            }
        }

        // Buscar en tabla global
        for ($i = 0; $i < count($this->symbolTable); $i++) {
            if ($this->symbolTable[$i]['identifier'] !== $identifier) {
                continue;
            }
            $symIsPointer = str_starts_with($this->symbolTable[$i]['type'], '*');
            if ($symIsPointer && !$newIsPointer) {
                continue;
            }
            $this->symbolTable[$i]['value'] = $newValue;
            return true;
        }

        return false;
    }

    /**
     * Verifica si un símbolo existe solo en el scope actual.
     */
    protected function symbolExistsInCurrentScope(string $identifier): bool
    {
        if (empty($this->scopeStack)) {
            foreach ($this->symbolTable as $symbol) {
                if ($symbol['identifier'] === $identifier && $symbol['scope'] === 'global') {
                    return true;
                }
            }
            return false;
        }

        $currentScope = $this->scopeStack[count($this->scopeStack) - 1];
        foreach ($currentScope['symbols'] as $symbol) {
            if ($symbol['identifier'] === $identifier) {
                return true;
            }
        }
        return false;
    }

    /**
     * Busca un símbolo en todo el árbol de scopes (desde actual hacia global).
     */
    protected function findSymbol(string $identifier): ?array
    {
        // Buscar en scopeStack desde el más reciente
        for ($i = count($this->scopeStack) - 1; $i >= 0; $i--) {
            foreach ($this->scopeStack[$i]['symbols'] as $symbol) {
                if ($symbol['identifier'] === $identifier) {
                    return $symbol;
                }
            }
        }

        // Buscar en tabla global
        foreach ($this->symbolTable as $symbol) {
            if ($symbol['identifier'] === $identifier) {
                return $symbol;
            }
        }

        return null;
    }

    /**
     * Retorna el nombre del scope actual.
     */
    protected function getCurrentScopeName(): string
    {
        if (empty($this->scopeStack)) {
            return 'global';
        }
        return $this->scopeStack[count($this->scopeStack) - 1]['name'];
    }

    /**
     * Verifica si un símbolo es constante.
     */
    protected function isConstant(string $identifier): bool
    {
        for ($i = count($this->scopeStack) - 1; $i >= 0; $i--) {
            foreach ($this->scopeStack[$i]['symbols'] as $symbol) {
                if ($symbol['identifier'] === $identifier) {
                    return isset($symbol['isConstant']) && $symbol['isConstant'];
                }
            }
        }

        foreach ($this->symbolTable as $symbol) {
            if ($symbol['identifier'] === $identifier) {
                return isset($symbol['isConstant']) && $symbol['isConstant'];
            }
        }

        return false;
    }

    /**
     * Obtiene el valor por defecto según el tipo (enunciado sección 3.2.3)
     */
    private function getDefaultValue(string $type)
    {
        return match($type) {
            'int32', 'rune' => 0,
            'float32' => 0.0,
            'bool' => false,
            'string' => '',
            'nil' => null,
            default => null,
        };
    }

    /**
     * Retorna la tabla de símbolos completa, ordenada por línea, columna y orden.
     */
    public function getSymbolTable(): array
    {
        $table = $this->symbolTable;
        usort($table, function ($a, $b) {
            $lineCmp = $a['line'] <=> $b['line'];
            if ($lineCmp !== 0) {
                return $lineCmp;
            }

            $colCmp = $a['column'] <=> $b['column'];
            if ($colCmp !== 0) {
                return $colCmp;
            }

            return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
        });

        return array_map(function ($symbol) {
            unset($symbol['order']);
            return $symbol;
        }, $table);
    }

    /**
     * Limpia toda la tabla de símbolos y el stack de scopes.
     */
    public function clearSymbolTable(): void
    {
        $this->symbolTable      = [];
        $this->scopeStack       = [];
        $this->currentScope     = 0;
        $this->declarationOrder = 0;
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