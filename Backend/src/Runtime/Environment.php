<?php

namespace Golampi\Runtime;

class Environment
{
    private array $values = [];
    private ?Environment $parent;

    public function __construct(?Environment $parent = null)
    {
        $this->parent = $parent;
    }

    public function define(string $name, Value $value): void
    {
        $this->values[$name] = $value;
    }

    public function get(string $name): ?Value
    {
        if (array_key_exists($name, $this->values)) {
            return $this->values[$name];
        }

        if ($this->parent !== null) {
            return $this->parent->get($name);
        }

        return null;
    }

    public function set(string $name, Value $value): bool
    {
        if (array_key_exists($name, $this->values)) {
            $this->values[$name] = $value;
            return true;
        }

        if ($this->parent !== null) {
            return $this->parent->set($name, $value);
        }

        return false;
    }

    public function exists(string $name): bool
    {
        if (array_key_exists($name, $this->values)) {
            return true;
        }

        if ($this->parent !== null) {
            return $this->parent->exists($name);
        }

        return false;
    }

    public function getParent(): ?Environment
    {
        return $this->parent;
    }
}
