<?php

namespace Golampi\Compiler\ARM64\FunctionContext;

/**
 * VariableLocation — Trackeador de ubicación de variables (registro vs stack)
 *
 * En lugar de asignar SIEMPRE stack offsets a variables escalares,
 * este módulo determina para cada variable si vive en:
 *   - Un registro específico (x0-x7, s0-s7)
 *   - O en stack (offset negativo desde fp)
 *
 * Estrategia de asignación (fases):
 *   1. Fase 1 (prescan): marcar variables como candidatas a registro
 *   2. Fase 2 (prescan late): asignar registros concretos mediante graph coloring
 *   3. Fase 3 (generation): si agotamos registros, spill al stack
 */
trait VariableLocation
{
    /**
     * Hash que mapea nombre_variable → ubicación.
     *
     * Ubicación:
     *   - 'register' => 'x0', 's1', etc.
     *   - 'stack'    => offset (8, 16, 24, ...)
     *   - 'null' si aún no asignado
     *
     * Formato: ['x' => ['location' => 'x0', 'type' => 'int32'], ...]
     */
    private array $variableLocations = [];

    /**
     * Registros disponibles para asignación de variables.
     * - Integers: x0-x7 (8 registros de trabajo, sin guardar)
     * - Floats:  s0-s7 (8  registros de trabajo, sin guardar)
     */
    private array $availableIntRegs   = ['x0', 'x1', 'x2', 'x3', 'x4', 'x5', 'x6', 'x7'];
    private array $availableFloatRegs = ['s0', 's1', 's2', 's3', 's4', 's5', 's6', 's7'];

    /**
     * Registros actualmente en uso (para evitar colisiones).
     */
    private array $usedIntRegs   = [];
    private array $usedFloatRegs = [];

    /**
     * Stack offset para variables que necesitan spill.
     */
    private int $spillOffset = 8;

    /**
     * Asigna una variable a un registro específico.
     * Si no hay registros disponibles, asigna stack.
     *
     * @param string $varName nombre de variable
     * @param string $type    tipo: 'int32', 'float32', etc.
     * @return string ubicación asignada: 'x0', 's1', etc. o 'stack@16'
     */
    public function assignVariable(string $varName, string $type): string
    {
        if (isset($this->variableLocations[$varName])) {
            return $this->variableLocations[$varName]['location'];
        }

        $location = null;

        // Intentar asignar a un registro
        if (in_array($type, ['int32', 'int8', 'int16', 'int64', 'rune', 'bool'])) {
            foreach ($this->availableIntRegs as $reg) {
                if (!in_array($reg, $this->usedIntRegs)) {
                    $location = $reg;
                    $this->usedIntRegs[] = $reg;
                    break;
                }
            }
        } elseif (in_array($type, ['float32', 'float64'])) {
            foreach ($this->availableFloatRegs as $reg) {
                if (!in_array($reg, $this->usedFloatRegs)) {
                    $location = $reg;
                    $this->usedFloatRegs[] = $reg;
                    break;
                }
            }
        }

        // Si no hay registros disponibles o es tipo complejo, asignar stack
        if ($location === null) {
            $location = 'stack@' . $this->spillOffset;
            $this->spillOffset += 8;
        }

        $this->variableLocations[$varName] = [
            'location' => $location,
            'type'     => $type,
        ];

        return $location;
    }

    /**
     * Obtiene la ubicación de una variable.
     * @return string registro o 'stack@N' o null
     */
    public function getVariableLocation(string $varName): ?string
    {
        return $this->variableLocations[$varName]['location'] ?? null;
    }

    /**
     * Verifica si una variable está en un registro.
     * @return bool true si está en registro, false si en stack
     */
    public function isInRegister(string $varName): bool
    {
        $loc = $this->getVariableLocation($varName);
        return $loc !== null && strpos($loc, 'stack@') === false;
    }

    /**
     * Verifica si una variable está en stack.
     */
    public function isInStack(string $varName): bool
    {
        $loc = $this->getVariableLocation($varName);
        return $loc !== null && strpos($loc, 'stack@') === 0;
    }

    /**
     * Obtiene cuántos bytes se necesitan en stack para spill.
     * (Solo para variables que NO cupieron en registros.)
     */
    public function getSpillSizeBytes(): int
    {
        if ($this->spillOffset <= 8) {
            return 0;
        }
        return $this->spillOffset - 8;  // Espacio usado desde 8 en adelante
    }

    /**
     * Obtiene el diccionario completo de ubicaciones (para debug).
     */
    public function getVariableLocations(): array
    {
        return $this->variableLocations;
    }

    /**
     * Limpia todas las ubicaciones (reset).
     */
    public function clearVariableLocations(): void
    {
        $this->variableLocations = [];
        $this->usedIntRegs   = [];
        $this->usedFloatRegs = [];
        $this->spillOffset   = 8;
    }

    /**
     * Libera un registro (cuando una variable deja de ser necesaria).
     * Útil para análisis de liveness.
     */
    public function freeRegister(string $reg): void
    {
        if (($key = array_search($reg, $this->usedIntRegs)) !== false) {
            unset($this->usedIntRegs[$key]);
        }
        if (($key = array_search($reg, $this->usedFloatRegs)) !== false) {
            unset($this->usedFloatRegs[$key]);
        }
    }
}
