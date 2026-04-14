<?php

namespace Golampi\Compiler\ARM64\Traits\RegisterAllocation;

use Golampi\Compiler\ARM64\Traits\RegisterAllocation\InterferenceGraph;

/**
 * GraphColoring — Algoritmo de Chaitin-Briggs para coloreo de grafos
 *
 * Colorea un grafo con K colores (registros) minimizando spills.
 *
 * Algoritmo:
 *
 *   1. SIMPLIFICATION PHASE (simplificación):
 *      - Mientras exista un nodo n con grado < K:
 *        * Push n a una pila
 *        * Remover n y sus aristas del grafo
 *   
 *   2. SPILLING PHASE (derrame):
 *      - Si todos los nodos tienen grado >= K:
 *        * Seleccionar nodo con mayor costo de spill
 *        * Push a pila
 *   
 *   3. SELECTION PHASE (selección):
 *      - Mientras pila no vacía:
 *        * Pop nodo n
 *        * Asignar color no usado por vecinos
 *        * Si no hay colores → spill a memoria
 *
 * Para expresiones binarias como (a - b):
 *   K = 2 (necesitamos s0 y s1)
 *   Ambas interfieren → grado = 1 cada una
 *   → Se colorean automáticamente sin spill
 */
class GraphColoring
{
    /** @var InterferenceGraph */
    private InterferenceGraph $graph;

    /** @var int Número de registros disponibles */
    private int $numColors;

    /** @var array Pila de simplificación */
    private array $stack = [];

    /** @var array Registros derramados (necesitan stack) */
    private array $spilledVars = [];

    public function __construct(InterferenceGraph $graph, int $numColors = 8)
    {
        $this->graph = $graph;
        $this->numColors = $numColors;
    }

    /**
     * Ejecuta el algoritmo completo de coloreo.
     * Retorna verdadero si se logró colorear sin spill, falso si hay spillage.
     */
    public function color(string $varType): bool
    {
        // Fase 1: SIMPLIFICATION
        $this->simplificationPhase($varType);

        // Fase 2: SELECTION
        $this->selectionPhase($varType);

        return empty($this->spilledVars);
    }

    /**
     * FASE 1: Simplificación
     * Elimina nodos con grado < K de forma recursiva.
     */
    private function simplificationPhase(string $varType): void
    {
        $nodes = $this->graph->getNodes();
        $workList = array_filter($nodes, fn($var) => 
            $this->graph->getDegree($var) < $this->numColors
        );

        while (!empty($workList)) {
            foreach ($workList as $index => $var) {
                // Push a pila
                array_push($this->stack, $var);

                // Remover del grafo (conceptualmente)
                // En esta impl., solo lo marcamos en la pila

                unset($workList[$index]);
                $workList = array_values($workList);

                // Detectar nuevos nodos de bajo grado
                $neighbors = $this->graph->getNeighbors($var);
                foreach ($neighbors as $neighbor) {
                    if (!$this->graph->isAllocated($neighbor) && 
                        $this->graph->getDegree($neighbor) >= $this->numColors) {
                        // Actualizar grado (heurística: decrementar después de quitar var)
                        if ($this->graph->getDegree($neighbor) - 1 < $this->numColors) {
                            $workList[] = $neighbor;
                        }
                    }
                }
            }
        }

        // Si aún quedan nodos no coloreados (grado >= K)
        $remaining = array_filter($this->graph->getNodes(), 
            fn($var) => !$this->graph->isAllocated($var)
        );
        foreach ($remaining as $var) {
            array_push($this->stack, $var);
        }
    }

    /**
     * FASE 2: Selección
     * Pop nodos de la pila y asigna colores (registros).
     */
    private function selectionPhase(string $varType): void
    {
        $availableRegs = $this->graph->getAvailableRegisters($varType);

        while (!empty($this->stack)) {
            $var = array_pop($this->stack);

            if ($this->graph->isAllocated($var)) {
                continue; // Ya coloreado
            }

            // Colores usados por vecinos
            $usedColors = [];
            foreach ($this->graph->getNeighbors($var) as $neighbor) {
                $reg = $this->graph->getRegister($neighbor);
                if ($reg !== null) {
                    $usedColors[$reg] = true;
                }
            }

            // Buscar color disponible
            $assignedReg = null;
            foreach ($availableRegs as $reg) {
                if (!isset($usedColors[$reg])) {
                    $assignedReg = $reg;
                    break;
                }
            }

            if ($assignedReg !== null) {
                // Colorear sin spill
                $this->graph->assign($var, $assignedReg);
            } else {
                // Spill: marcar para stack
                $this->spilledVars[$var] = true;
                // Como fallback, usar el primer registro (con spill later)
                $this->graph->assign($var, $availableRegs[0] ?? 'x0');
            }
        }
    }

    /**
     * Retorna variables que fueron derramadas (necesitan stack).
     */
    public function getSpilledVariables(): array
    {
        return array_keys($this->spilledVars);
    }

    /**
     * Retorna si hubo spillage.
     */
    public function hadSpill(): bool
    {
        return !empty($this->spilledVars);
    }
}
