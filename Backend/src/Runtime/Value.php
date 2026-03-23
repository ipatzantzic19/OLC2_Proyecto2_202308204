<?php

namespace Golampi\Runtime;

class Value
{
    private string $type;
    private $value;

    public function __construct(string $type, $value)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function isNil(): bool
    {
        return $this->type === 'nil' || $this->value === null;
    }

    public function toBool(): bool
    {
        if ($this->isNil())            return false;
        if ($this->type === 'bool')    return $this->value;
        if ($this->type === 'int32')   return $this->value !== 0;
        if ($this->type === 'float32') return $this->value !== 0.0;
        if ($this->type === 'string')  return $this->value !== '';
        if ($this->type === 'array')   return true;   // array no vacío = truthy
        return true;
    }

    /**
     * Representación de cadena del valor.
     * Para arreglos se usa el formato [e0 e1 e2 …] (igual que Go con fmt.Println).
     */
    public function toString(): string
    {
        if ($this->isNil())   return '<nil>';
        if ($this->type === 'bool')    return $this->value ? 'true' : 'false';
        if ($this->type === 'rune')    return (string) $this->value;   // código Unicode numérico
        if ($this->type === 'string')  return $this->value;
        if ($this->type === 'array')   return $this->arrayToString();
        if ($this->type === 'pointer') return 'pointer';
        return (string) $this->value;
    }

    /**
     * Formatea el arreglo de forma recursiva.
     * Ejemplo de salida: [1 2 3]  /  [[1 2] [3 4]]
     */
    private function arrayToString(): string
    {
        $data  = $this->value;
        $parts = [];

        foreach ($data['elements'] as $el) {
            /** @var Value $el */
            $parts[] = $el->toString();
        }

        return '[' . implode(' ', $parts) . ']';
    }

    // =========================================================
    //  CONSTRUCTORES ESTÁTICOS
    // =========================================================

    public static function nil(): Value
    {
        return new Value('nil', null);
    }

    public static function int32(int $value): Value
    {
        return new Value('int32', $value);
    }

    public static function float32(float $value): Value
    {
        return new Value('float32', $value);
    }

    public static function bool(bool $value): Value
    {
        return new Value('bool', $value);
    }

    public static function string(string $value): Value
    {
        return new Value('string', $value);
    }

    public static function rune(int $value): Value
    {
        return new Value('rune', $value);
    }

    /**
     * Múltiples valores de retorno (tuple).
     * @param Value[] $values
     */
    public static function multi(array $values): Value
    {
        return new Value('multi', $values);
    }

    /**
     * Puntero a una variable en un entorno dado.
     */
    public static function pointer(string $varName, Environment $env): Value
    {
        return new Value('pointer', ['varName' => $varName, 'env' => $env]);
    }

    /**
     * Crea un arreglo con su estructura interna.
     *
     * @param string  $elementType  Tipo de los elementos ('int32', 'array', etc.)
     * @param int     $size         Número de elementos
     * @param Value[] $elements     Elementos del arreglo
     */
    public static function array(string $elementType, int $size, array $elements): Value
    {
        return new Value('array', [
            'elementType' => $elementType,
            'size'        => $size,
            'elements'    => $elements,
        ]);
    }
}