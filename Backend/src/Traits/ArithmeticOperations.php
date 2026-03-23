<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;

/**
 * Trait para manejar operaciones aritméticas según la tabla de compatibilidad
 */
trait ArithmeticOperations
{
    /**
     * Realiza una suma entre dos valores
     */
    protected function performAddition(Value $left, Value $right, int $line = 0, int $column = 0): Value
    {
        $leftType = $left->getType();
        $rightType = $right->getType();

        // nil + cualquier cosa = nil
        if ($left->isNil() || $right->isNil()) {
            return Value::nil();
        }

        // int32 + int32 = int32
        if ($leftType === 'int32' && $rightType === 'int32') {
            return Value::int32($left->getValue() + $right->getValue());
        }

        // int32 + float32 = float32
        if ($leftType === 'int32' && $rightType === 'float32') {
            return Value::float32((float)$left->getValue() + $right->getValue());
        }

        // float32 + int32 = float32
        if ($leftType === 'float32' && $rightType === 'int32') {
            return Value::float32($left->getValue() + (float)$right->getValue());
        }

        // float32 + float32 = float32
        if ($leftType === 'float32' && $rightType === 'float32') {
            return Value::float32($left->getValue() + $right->getValue());
        }

        // int32 + rune = int32
        if ($leftType === 'int32' && $rightType === 'rune') {
            return Value::int32($left->getValue() + $right->getValue());
        }

        // rune + int32 = int32
        if ($leftType === 'rune' && $rightType === 'int32') {
            return Value::int32($left->getValue() + $right->getValue());
        }

        // string + string = string (concatenación)
        if ($leftType === 'string' && $rightType === 'string') {
            return Value::string($left->getValue() . $right->getValue());
        }

        // Operación inválida - generar error semántico
        $this->addSemanticError(
            "Operación no válida: no se puede sumar '$leftType' + '$rightType'",
            $line,
            $column
        );
        return Value::nil();
    }

    /**
     * Realiza una resta entre dos valores
     */
    protected function performSubtraction(Value $left, Value $right, int $line = 0, int $column = 0): Value
    {
        $leftType = $left->getType();
        $rightType = $right->getType();

        if ($left->isNil() || $right->isNil()) {
            return Value::nil();
        }

        // int32 - int32 = int32
        if ($leftType === 'int32' && $rightType === 'int32') {
            return Value::int32($left->getValue() - $right->getValue());
        }

        // int32 - float32 = float32
        if ($leftType === 'int32' && $rightType === 'float32') {
            return Value::float32((float)$left->getValue() - $right->getValue());
        }

        // float32 - int32 = float32
        if ($leftType === 'float32' && $rightType === 'int32') {
            return Value::float32($left->getValue() - (float)$right->getValue());
        }

        // float32 - float32 = float32
        if ($leftType === 'float32' && $rightType === 'float32') {
            return Value::float32($left->getValue() - $right->getValue());
        }

        // int32 - rune = int32
        if ($leftType === 'int32' && $rightType === 'rune') {
            return Value::int32($left->getValue() - $right->getValue());
        }

        // rune - int32 = int32
        if ($leftType === 'rune' && $rightType === 'int32') {
            return Value::int32($left->getValue() - $right->getValue());
        }

        // Operación inválida
        $this->addSemanticError(
            "Operación no válida: no se puede restar '$leftType' - '$rightType'",
            $line,
            $column
        );
        return Value::nil();
    }

    /**
     * Realiza una multiplicación entre dos valores
     */
    protected function performMultiplication(Value $left, Value $right, int $line = 0, int $column = 0): Value
    {
        $leftType = $left->getType();
        $rightType = $right->getType();

        if ($left->isNil() || $right->isNil()) {
            return Value::nil();
        }

        // int32 * int32 = int32
        if ($leftType === 'int32' && $rightType === 'int32') {
            return Value::int32($left->getValue() * $right->getValue());
        }

        // int32 * float32 = float32
        if ($leftType === 'int32' && $rightType === 'float32') {
            return Value::float32((float)$left->getValue() * $right->getValue());
        }

        // float32 * int32 = float32
        if ($leftType === 'float32' && $rightType === 'int32') {
            return Value::float32($left->getValue() * (float)$right->getValue());
        }

        // float32 * float32 = float32
        if ($leftType === 'float32' && $rightType === 'float32') {
            return Value::float32($left->getValue() * $right->getValue());
        }

        // int32 * string = string (repetición)
        if ($leftType === 'int32' && $rightType === 'string') {
            return Value::string(str_repeat($right->getValue(), $left->getValue()));
        }

        // string * int32 = string (repetición)
        if ($leftType === 'string' && $rightType === 'int32') {
            return Value::string(str_repeat($left->getValue(), $right->getValue()));
        }

        // Operación inválida
        $this->addSemanticError(
            "Operación no válida: no se puede multiplicar '$leftType' * '$rightType'",
            $line,
            $column
        );
        return Value::nil();
    }

    /**
     * Realiza una división entre dos valores
     */
    protected function performDivision(Value $left, Value $right, int $line = 0, int $column = 0): Value
    {
        $leftType = $left->getType();
        $rightType = $right->getType();

        if ($left->isNil() || $right->isNil()) {
            return Value::nil();
        }

        // División por cero
        if (($rightType === 'int32' || $rightType === 'float32') && $right->getValue() == 0) {
            return Value::nil();
        }

        // int32 / int32 = int32
        if ($leftType === 'int32' && $rightType === 'int32') {
            return Value::int32(intdiv($left->getValue(), $right->getValue()));
        }

        // int32 / float32 = float32
        if ($leftType === 'int32' && $rightType === 'float32') {
            return Value::float32((float)$left->getValue() / $right->getValue());
        }

        // float32 / int32 = float32
        if ($leftType === 'float32' && $rightType === 'int32') {
            return Value::float32($left->getValue() / (float)$right->getValue());
        }

        // float32 / float32 = float32
        if ($leftType === 'float32' && $rightType === 'float32') {
            return Value::float32($left->getValue() / $right->getValue());
        }

        // Operación inválida
        $this->addSemanticError(
            "Operación no válida: no se puede dividir '$leftType' / '$rightType'",
            $line,
            $column
        );
        return Value::nil();
    }

    /**
     * Realiza operación módulo entre dos valores
     */
    protected function performModulo(Value $left, Value $right, int $line = 0, int $column = 0): Value
    {
        $leftType = $left->getType();
        $rightType = $right->getType();

        if ($left->isNil() || $right->isNil()) {
            return Value::nil();
        }

        // Módulo por cero
        if (($rightType === 'int32' || $rightType === 'rune') && $right->getValue() == 0) {
            return Value::nil();
        }

        // int32 % int32 = int32
        if ($leftType === 'int32' && $rightType === 'int32') {
            return Value::int32($left->getValue() % $right->getValue());
        }

        // int32 % rune = int32
        if ($leftType === 'int32' && $rightType === 'rune') {
            return Value::int32($left->getValue() % $right->getValue());
        }

        // rune % int32 = int32
        if ($leftType === 'rune' && $rightType === 'int32') {
            return Value::int32($left->getValue() % $right->getValue());
        }

        // Operación inválida
        $this->addSemanticError(
            "Operación no válida: no se puede calcular módulo '$leftType' % '$rightType'",
            $line,
            $column
        );
        return Value::nil();
    }
}