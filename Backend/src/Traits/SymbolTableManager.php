<?php

namespace Golampi\Traits;

trait SymbolTableManager
{
    protected array $symbolTable = [];
    protected int $currentScope = 0;
    protected array $scopeStack = [];
    private int $declarationOrder = 0;

    protected function enterScope(string $scopeName): void
    {
        $this->currentScope++;
        $this->scopeStack[] = [
            'id'      => $this->currentScope,
            'name'    => $scopeName,
            'symbols' => []
        ];
    }

    protected function exitScope(): void
    {
        if (empty($this->scopeStack)) {
            return;
        }

        $scope     = array_pop($this->scopeStack);
        $scopeName = $scope['name'];

        // Scopes de función (function:X) vuelcan sus símbolos directamente
        // a $this->symbolTable para evitar que variables internas de una
        // función contaminen el scope del llamador cuando éste también es
        // una función.
        //
        // Scopes de bloque/ciclo (for, if-block, else-block, switch)
        // se fusionan en el scope padre del stack porque sus variables
        // pertenecen semánticamente a la función que los contiene.
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
     * Fusiona $symbol en $target (array por referencia).
     * Si ya existe la misma declaración (id + scope + línea + col)
     * solo actualiza el valor. Si no existe, agrega como nueva entrada.
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

    protected function addSymbol(
        string $identifier,
        string $type,
        string $scope,
        $value,
        int $line,
        int $column
    ): bool {
        if ($this->symbolExistsInCurrentScope($identifier)) {
            return false;
        }

        $symbol = [
            'identifier' => $identifier,
            'type'       => $type,
            'scope'      => $scope,
            'value'      => $value,
            'line'       => $line,
            'column'     => $column,
            'order'      => $this->declarationOrder++
        ];

        if (!empty($this->scopeStack)) {
            $this->scopeStack[count($this->scopeStack) - 1]['symbols'][] = $symbol;
        } else {
            $this->symbolTable[] = $symbol;
        }

        return true;
    }

    protected function updateSymbolValue(string $identifier, $newValue): bool
    {
        // Si el nuevo valor no es un puntero, no sobrescribir entradas cuyo
        // tipo ES un puntero (*T). Esto evita que una asignación via puntero
        // (*p = val) corrompa el registro del parámetro puntero en la tabla
        // en lugar de actualizar la variable apuntada.
        $newIsPointer = ($newValue instanceof \Golampi\Runtime\Value)
            && $newValue->getType() === 'pointer';

        for ($i = count($this->scopeStack) - 1; $i >= 0; $i--) {
            $symbols = &$this->scopeStack[$i]['symbols'];
            for ($j = 0; $j < count($symbols); $j++) {
                if ($symbols[$j]['identifier'] !== $identifier) {
                    continue;
                }
                // Saltar parámetros/variables puntero cuando el valor nuevo no es puntero
                $symIsPointer = str_starts_with($symbols[$j]['type'], '*');
                if ($symIsPointer && !$newIsPointer) {
                    continue;
                }
                $symbols[$j]['value'] = $newValue;
                return true;
            }
        }

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

    protected function symbolExistsInCurrentScope(string $identifier): bool
    {
        if (empty($this->scopeStack)) {
            foreach ($this->symbolTable as $symbol) {
                if ($symbol['identifier'] === $identifier
                    && $symbol['scope'] === 'global'
                ) {
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

    protected function findSymbol(string $identifier): ?array
    {
        for ($i = count($this->scopeStack) - 1; $i >= 0; $i--) {
            foreach ($this->scopeStack[$i]['symbols'] as $symbol) {
                if ($symbol['identifier'] === $identifier) {
                    return $symbol;
                }
            }
        }
        foreach ($this->symbolTable as $symbol) {
            if ($symbol['identifier'] === $identifier) {
                return $symbol;
            }
        }
        return null;
    }

    protected function getCurrentScopeName(): string
    {
        if (empty($this->scopeStack)) {
            return 'global';
        }
        return $this->scopeStack[count($this->scopeStack) - 1]['name'];
    }

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

            return $a['order'] <=> $b['order'];
        });
        return array_map(function ($symbol) {
            unset($symbol['order']);
            return $symbol;
        }, $table);
    }

    public function clearSymbolTable(): void
    {
        $this->symbolTable      = [];
        $this->scopeStack       = [];
        $this->currentScope     = 0;
        $this->declarationOrder = 0;
    }

    protected function isConstant(string $identifier): bool
    {
        for ($i = count($this->scopeStack) - 1; $i >= 0; $i--) {
            foreach ($this->scopeStack[$i]['symbols'] as $symbol) {
                if ($symbol['identifier'] === $identifier) {
                    return str_contains($symbol['type'], '(const)');
                }
            }
        }
        foreach ($this->symbolTable as $symbol) {
            if ($symbol['identifier'] === $identifier) {
                return str_contains($symbol['type'], '(const)');
            }
        }
        return false;
    }
}