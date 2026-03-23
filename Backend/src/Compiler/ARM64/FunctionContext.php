<?php

namespace Golampi\Compiler\ARM64;

/**
 * Contexto de compilación de una función.
 * Lleva registro de variables locales, sus offsets en el stack frame
 * y meta-información necesaria para generar el prólogo/epílogo ARM64.
 *
 * Layout del stack frame (AArch64):
 *
 *  [dirección alta]
 *  ┌──────────────┐ ← sp_original (antes del stp)
 *  │  x29 (saved) │ ← [fp + 0]
 *  │  x30 (saved) │ ← [fp + 8]
 *  ├──────────────┤ ← x29 = fp  (frame pointer)
 *  │  local var 1 │ ← [fp - 8]
 *  │  local var 2 │ ← [fp - 16]
 *  │     ...      │
 *  └──────────────┘ ← sp  (fp - FRAME_SIZE)
 *  [dirección baja]
 *
 * Cada variable ocupa 8 bytes (simplificación de Fase 1 para alineación).
 * Fase 1 soporta hasta 32 variables locales por función (256 bytes).
 */
class FunctionContext
{
    public  string $name;
    public  string $epilogueLabel = '';

    private array  $locals     = [];  // name => ['offset' => int, 'type' => string]
    private int    $nextOffset = 8;   // El primer var queda en [fp - 8]

    /** Tamaño del frame de locales (sin contar x29/x30 guardados por stp) */
    const MAX_FRAME = 256;  // 32 variables × 8 bytes — Fase 1

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Registra una variable local y devuelve su offset.
     * Si ya existe (re-declaración en mismo scope), devuelve el offset existente.
     */
    public function allocLocal(string $name, string $type): int
    {
        if (isset($this->locals[$name])) {
            // Actualizar el tipo si era 'unknown'
            if ($this->locals[$name]['type'] === 'unknown') {
                $this->locals[$name]['type'] = $type;
            }
            return $this->locals[$name]['offset'];
        }

        $offset = $this->nextOffset;
        $this->locals[$name] = ['offset' => $offset, 'type' => $type];
        $this->nextOffset += 8;

        return $offset;
    }

    public function hasLocal(string $name): bool
    {
        return isset($this->locals[$name]);
    }

    public function getOffset(string $name): int
    {
        return $this->locals[$name]['offset'] ?? 0;
    }

    public function getType(string $name): string
    {
        return $this->locals[$name]['type'] ?? 'int32';
    }

    public function setType(string $name, string $type): void
    {
        if (isset($this->locals[$name]) && $type !== 'unknown') {
            $this->locals[$name]['type'] = $type;
        }
    }

    /**
     * Tamaño del frame redondeado a múltiplo de 16 (obligatorio en AArch64).
     * Devuelve 0 si no hay variables locales.
     */
    public function getFrameSize(): int
    {
        $localBytes = $this->nextOffset - 8;  // Bytes usados por variables
        if ($localBytes <= 0) {
            return 0;
        }
        // Redondear hacia arriba al múltiplo de 16 más cercano
        return (int)(ceil($localBytes / 16) * 16);
    }

    public function getLocalCount(): int
    {
        return count($this->locals);
    }

    public function isFrameFull(): bool
    {
        return ($this->nextOffset - 8) >= self::MAX_FRAME;
    }
}