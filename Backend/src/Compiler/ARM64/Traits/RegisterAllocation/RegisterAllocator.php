<?php

namespace Golampi\Compiler\ARM64\Traits\RegisterAllocation;

use Golampi\Compiler\ARM64\Traits\RegisterAllocation\InterferenceGraph;
use Golampi\Compiler\ARM64\Traits\RegisterAllocation\GraphColoring;

/**
 * RegisterAllocator — Trait orquestador de asignación de registros
 *
 * Orquesta el flujo completo de asignación de registros siguiendo AHU Cap. 8-9:
 *
 *   1. Análisis de liveness
 *   2. Construcción del grafo de interferencia
 *   3. Coloreo de grafos (Chaitin-Briggs)
 *   4. Generación de código optimizado
 *
 * Uso típico en expresiones binarias:
 *
 *   // Antes (ineficiente con push/pop stack):
 *   $this->pushFloatStack();
 *   $rhsType = $this->visit($rhs);
 *   $this->popFloatStack();
 *
 *   // Después (óptimo sin spill):
 *   $allocation = $this->allocateRegisterPair('float32');
 *   $reg1 = $allocation['lhs']; // s0
 *   $reg2 = $allocation['rhs']; // s1
 *   $this->emit("fsub $reg1, $reg1, $reg2");
 */
trait RegisterAllocator
{
    /** @var InterferenceGraph Grafo actual de interferencia */
    private ?InterferenceGraph $currentGraph = null;

    /** @var array<string, string> Cache de asignaciones recientes */
    private array $allocationCache = [];

    /**
     * Asigna registros óptimos para una operación binaria.
     * 
     * Retorna: array{lhs: string, rhs: string, spillNeeded: bool}
     */
    public function allocateRegisterPair(string $varType): array
    {
        $graph = new InterferenceGraph();
        $coloring = new GraphColoring($graph, $this->getRegisterCount($varType));

        // Para una operación binaria simple:
        // - Nodo 1: variable izquierda (lhs)
        // - Nodo 2: variable derecha (rhs)
        // - Arista: ambas interfieren (están vivas simultáneamente)

        $graph->addNode('lhs', $varType);
        $graph->addNode('rhs', $varType);
        $graph->addEdge('lhs', 'rhs');

        // Colorear (asignar registros)
        $hadSpill = !$coloring->color($varType);

        $lhsReg = $graph->getRegister('lhs') ?? $this->getDefaultRegister($varType, 0);
        $rhsReg = $graph->getRegister('rhs') ?? $this->getDefaultRegister($varType, 1);

        return [
            'lhs'       => $lhsReg,
            'rhs'       => $rhsReg,
            'spillNeeded' => $hadSpill,
        ];
    }

    /**
     * Asigna registros para múltiples variables en una expresión compleja.
     *
     * Retorna: array<varName => register>
     */
    public function allocateRegistersForExpression(array $variables, string $varType): array
    {
        $graph = new InterferenceGraph();

        // Paso 1: Agregar todos los nodos
        foreach ($variables as $var) {
            $graph->addNode($var, $varType);
        }

        // Paso 2: Construir interferencias (por simplicidad: todas interfieren)
        // En un análisis real, hacer liveness analysis completo
        for ($i = 0; $i < count($variables); $i++) {
            for ($j = $i + 1; $j < count($variables); $j++) {
                $graph->addEdge($variables[$i], $variables[$j]);
            }
        }

        // Paso 3: Colorear
        $coloring = new GraphColoring($graph, $this->getRegisterCount($varType));
        $coloring->color($varType);

        // Paso 4: Extraer asignaciones
        $allocation = [];
        $defaultRegs = $this->getDefaultRegisters($varType, count($variables));
        
        foreach ($variables as $index => $var) {
            $allocation[$var] = $graph->getRegister($var) ?? 
                                $defaultRegs[$index] ?? 
                                $this->getDefaultRegister($varType, 0);
        }

        return $allocation;
    }

    /**
     * Retorna número de registros disponibles para un tipo.
     */
    private function getRegisterCount(string $varType): int
    {
        return match ($varType) {
            'int32' => 8,      // x0-x7 caller-saved
            'float32' => 10,   // s0-s9 caller-saved
            'string' => 4,     // x0-x3
            default => 1,
        };
    }

    /**
     * Retorna registro por defecto en una posición.
     */
    private function getDefaultRegister(string $varType, int $position): string
    {
        $regs = $this->getDefaultRegisters($varType, 10);
        return $regs[$position] ?? 'x0';
    }

    /**
     * Retorna lista de registros por defecto para un tipo.
     */
    private function getDefaultRegisters(string $varType, int $count): array
    {
        return match ($varType) {
            'int32' => ['x0', 'x1', 'x2', 'x3', 'x4', 'x5', 'x6', 'x7'],
            'float32' => ['s0', 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9'],
            'string' => ['x0', 'x1', 'x2', 'x3'],
            default => ['x0'],
        };
    }
}
