<?php

namespace Golampi\Compiler\ARM64\Traits\RegisterAllocation;

/**
 * InterferenceGraph — Construcción del grafo de interferencia
 *
 * Un nodo por variable; una arista entre dos variables si interfieren
 * (sus rangos de liveness se solapan).
 *
 * Para expresiones binarias como (a - b):
 *   Nodos: {a, b}
 *   Aristas: a↔b (ambos están vivos al leerlos)
 *   Cromático: 2 colores mínimo (dos registros diferentes)
 *
 * Ejemplo con (a + b - c):
 *   Nodos: {a, b, c}
 *   Aristas: a↔b, a↔c, b↔c (todos interfieren)
 *   Cromático: 3 colores mínimo
 */
class InterferenceGraph
{
    /** @var array<string, set<string>> Adyacencia del grafo */
    private array $edges = [];

    /** @var array<string, string> Registros asignados a cada variable */
    private array $allocation = [];

    /** @var array<int, array<string>> Registros disponibles por tipo */
    private array $registerPool = [
        'int32'  => ['x0', 'x1', 'x2', 'x3', 'x4', 'x5', 'x6', 'x7'],
        'float32' => ['s0', 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9'],
        'string'  => ['x0', 'x1', 'x2', 'x3'],
    ];

    public function __construct()
    {
        $this->edges = [];
        $this->allocation = [];
    }

    /**
     * Agregar nodo (variable) al grafo.
     */
    public function addNode(string $varName, string $type): void
    {
        if (!isset($this->edges[$varName])) {
            $this->edges[$varName] = [];
        }
    }

    /**
     * Agregar arista de interferencia entre dos variables.
     * Asegura simetría: si a interfiere con b, también b interfiere con a.
     */
    public function addEdge(string $var1, string $var2): void
    {
        if ($var1 === $var2) return; // Sin auto-aristas

        if (!isset($this->edges[$var1])) {
            $this->edges[$var1] = [];
        }
        if (!isset($this->edges[$var2])) {
            $this->edges[$var2] = [];
        }

        $this->edges[$var1][$var2] = true;
        $this->edges[$var2][$var1] = true;
    }

    /**
     * Retorna grado (número de vecinos) de un nodo.
     */
    public function getDegree(string $var): int
    {
        return isset($this->edges[$var]) ? count($this->edges[$var]) : 0;
    }

    /**
     * Retorna vecinos de un nodo.
     */
    public function getNeighbors(string $var): array
    {
        return isset($this->edges[$var]) ? array_keys($this->edges[$var]) : [];
    }

    /**
     * Retorna todos los nodos.
     */
    public function getNodes(): array
    {
        return array_keys($this->edges);
    }

    /**
     * Retorna la asignación de registros realizada.
     */
    public function getAllocation(): array
    {
        return $this->allocation;
    }

    /**
     * Asignar un registro a una variable.
     */
    public function assign(string $var, string $register): void
    {
        $this->allocation[$var] = $register;
    }

    /**
     * Obtener registro asignado a una variable.
     */
    public function getRegister(string $var): ?string
    {
        return $this->allocation[$var] ?? null;
    }

    /**
     * Verificar si una variable tiene registro asignado.
     */
    public function isAllocated(string $var): bool
    {
        return isset($this->allocation[$var]);
    }

    /**
     * Obtener registros disponibles para un tipo.
     */
    public function getAvailableRegisters(string $type): array
    {
        return $this->registerPool[$type] ?? [];
    }

    /**
     * Retorna visualización del grafo para debugging.
     */
    public function toString(): string
    {
        $str = "Interference Graph:\n";
        foreach ($this->edges as $var => $neighbors) {
            $str .= "  $var → " . implode(', ', array_keys($neighbors)) . "\n";
        }
        return $str;
    }
}
