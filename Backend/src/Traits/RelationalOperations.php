<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;

/**
 * Trait para manejar operaciones relacionales y lógicas
 */
trait RelationalOperations
{
    /**
     * Realiza una comparación entre dos valores
     */
    protected function performComparison(string $operator, Value $left, Value $right): Value
    {
        // Validar que los argumentos sean instancias de Value
        if (!$left instanceof Value || !$right instanceof Value) {
            throw new \TypeError(sprintf(
                "performComparison(): Ambos argumentos deben ser instancias de 'Golampi\\Runtime\\Value', se recibieron '%s' y '%s'.",
                is_object($left) ? get_class($left) : gettype($left),
                is_object($right) ? get_class($right) : gettype($right)
            ));
        }

        if ($left->isNil() || $right->isNil()) {
            return Value::nil();
        }

        $result = false;

        switch ($operator) {
            case '==':
            case '!=':
                $result = $this->compareEquality($left, $right, $operator === '==');
                break;
            case '>':
            case '>=':
            case '<':
            case '<=':
                $result = $this->compareRelational($operator, $left, $right);
                break;
        }

        return Value::bool($result);
    }

    /**
     * Compara igualdad/desigualdad entre dos valores
     */
    private function compareEquality(Value $left, Value $right, bool $equals): bool
    {
        $leftType = $left->getType();
        $rightType = $right->getType();

        // Comparaciones válidas
        if (($leftType === 'int32' && ($rightType === 'int32' || $rightType === 'float32' || $rightType === 'rune')) ||
            ($leftType === 'float32' && ($rightType === 'int32' || $rightType === 'float32' || $rightType === 'rune')) ||
            ($leftType === 'rune' && ($rightType === 'int32' || $rightType === 'float32' || $rightType === 'rune')) ||
            ($leftType === 'string' && $rightType === 'string') ||
            ($leftType === 'bool' && $rightType === 'bool')) {
            
            $isEqual = $left->getValue() == $right->getValue();
            return $equals ? $isEqual : !$isEqual;
        }

        return false;
    }

    /**
     * Compara valores con operadores relacionales (>, <, >=, <=)
     */
    private function compareRelational(string $operator, Value $left, Value $right): bool
    {
        $leftType = $left->getType();
        $rightType = $right->getType();

        // Solo números y strings son comparables con >, <, >=, <=
        if (($leftType === 'int32' || $leftType === 'float32' || $leftType === 'rune') &&
            ($rightType === 'int32' || $rightType === 'float32' || $rightType === 'rune')) {
            
            $leftVal = $left->getValue();
            $rightVal = $right->getValue();

            switch ($operator) {
                case '>': return $leftVal > $rightVal;
                case '>=': return $leftVal >= $rightVal;
                case '<': return $leftVal < $rightVal;
                case '<=': return $leftVal <= $rightVal;
            }
        }

        if ($leftType === 'string' && $rightType === 'string') {
            $cmp = strcmp($left->getValue(), $right->getValue());
            switch ($operator) {
                case '>': return $cmp > 0;
                case '>=': return $cmp >= 0;
                case '<': return $cmp < 0;
                case '<=': return $cmp <= 0;
            }
        }

        return false;
    }

    /**
     * Operador lógico AND con cortocircuito
     */
    protected function performLogicalAnd(Value $left, callable $rightEval): Value
    {
        if (!$left instanceof Value) {
            return Value::nil();
        }

        // Cortocircuito: si left es false, no evaluar right
        if (!$left->toBool()) {
            return Value::bool(false);
        }

        $right = $rightEval();
        
        if (!$right instanceof Value) {
            return Value::nil();
        }

        return Value::bool($right->toBool());
    }

    /**
     * Operador lógico OR con cortocircuito
     */
    protected function performLogicalOr(Value $left, callable $rightEval): Value
    {
        if (!$left instanceof Value) {
            return Value::nil();
        }

        // Cortocircuito: si left es true, no evaluar right
        if ($left->toBool()) {
            return Value::bool(true);
        }

        $right = $rightEval();
        
        if (!$right instanceof Value) {
            return Value::nil();
        }

        return Value::bool($right->toBool());
    }
}