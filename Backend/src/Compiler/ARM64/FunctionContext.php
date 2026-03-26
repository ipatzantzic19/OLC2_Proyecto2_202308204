<?php

namespace Golampi\Compiler\ARM64;

/**
 * FunctionContext — registro de activación de una función Golampi.
 *
 * Modelo de memoria en AArch64 (conforme a libro de compiladores Aho et al.):
 *
 *  [dirección alta]
 *  ┌──────────────────────┐ ← sp_original (antes del stp)
 *  │  x29 (saved FP)      │ ← [fp + 0]    stp x29,x30,[sp,#-16]!
 *  │  x30 (saved LR)      │ ← [fp + 8]
 *  ├──────────────────────┤ ← x29 = fp  (frame pointer / enlace de control)
 *  │  callee-saved regs   │ ← [fp - 8 .. fp - N*8]   si se usan x19-x28
 *  ├──────────────────────┤
 *  │  parámetros locales  │  (copiados desde x0-x7 / s0-s7 al inicio)
 *  │  variables locales   │  cada una 8 bytes alineados
 *  ├──────────────────────┤
 *  │  float locals (32b)  │  almacenados en 8 bytes por alineación
 *  ├──────────────────────┤
 *  │  temporales expr.    │  espacio reservado para expresiones complejas
 *  └──────────────────────┘ ← sp (fp - FRAME_SIZE)
 *  [dirección baja]
 *
 * Descriptores (Aho, Lam, Sethi — "Compiladores"):
 *  - descriptor de variable: offset en stack + tipo + si está "dirty" (modificada)
 *  - tipo del slot: distingue int32 vs float32 para emitir ldr/ldr-s correctamente
 *
 * Fase 2: añade soporte para float32 (slots de 8 bytes, acceso con ldr/str word),
 *         callee-saved registers, y tabla de firmas de funciones para multi-retorno.
 */
class FunctionContext
{
    public  string $name;
    public  string $epilogueLabel = '';

    // ── Tabla de variables locales ────────────────────────────────────────────
    // name => ['offset' => int, 'type' => string, 'is_param' => bool]
    private array  $locals     = [];
    private int    $nextOffset = 8;   // primer slot: [fp - 8]

    // ── Callee-saved registers en uso ─────────────────────────────────────────
    // x19-x28 que esta función necesita preservar
    private array  $calleeSaved = [];   // e.g. ['x19', 'x20']
    private int    $calleeSavedBytes = 0;

    // ── Arrays: metadatos para acceso multidimensional ────────────────────────
    // name => ['base_offset'=>int, 'dims'=>int[], 'elem_type'=>string, 'total_slots'=>int]
    private array  $arrays = [];

    // ── Retornos múltiples ────────────────────────────────────────────────────
    // cuántos valores retorna esta función y sus tipos
    public  array  $returnTypes = [];   // e.g. ['int32', 'bool']

    // ── Temporales en stack ───────────────────────────────────────────────────
    // espacio reservado al inicio del frame para expresiones complejas
    // En Fase 2 usamos el stack pointer dinámico (sub/add sp) en vez de
    // un área fija, pero registramos el uso máximo para el prescan.
    private int    $maxTempDepth = 0;
    private int    $currentTempDepth = 0;

    /**
     * Límite de frame en bytes.
     * Fase 1: 256 bytes (32 vars × 8).
     * Fase 2: 2048 bytes para soportar más vars + arrays medianos.
     */
    const MAX_FRAME = 2048;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  VARIABLES LOCALES
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Registra una variable escalar y devuelve su offset desde fp.
     * Si ya existe, devuelve el offset existente (re-declaración en mismo scope).
     *
     * Tipos float32 también ocupan 8 bytes en stack (alineación AArch64).
     */
    public function allocLocal(string $name, string $type, bool $isParam = false): int
    {
        if (isset($this->locals[$name])) {
            // Actualizar tipo si era 'unknown'
            if ($this->locals[$name]['type'] === 'unknown') {
                $this->locals[$name]['type'] = $type;
            }
            return $this->locals[$name]['offset'];
        }

        $offset = $this->nextOffset;
        $this->locals[$name] = [
            'offset'   => $offset,
            'type'     => $type,
            'is_param' => $isParam,
        ];
        $this->nextOffset += 8;
        return $offset;
    }

    public function hasLocal(string $name): bool
    {
        return isset($this->locals[$name]) || isset($this->arrays[$name]);
    }

    public function getOffset(string $name): int
    {
        if (isset($this->locals[$name])) {
            return $this->locals[$name]['offset'];
        }
        if (isset($this->arrays[$name])) {
            return $this->arrays[$name]['base_offset'];
        }
        return 0;
    }

    public function getType(string $name): string
    {
        if (isset($this->locals[$name])) {
            return $this->locals[$name]['type'];
        }
        if (isset($this->arrays[$name])) {
            return 'array';
        }
        return 'int32';
    }

    public function setType(string $name, string $type): void
    {
        if (isset($this->locals[$name]) && $type !== 'unknown') {
            $this->locals[$name]['type'] = $type;
        }
    }

    public function isFloat(string $name): bool
    {
        return ($this->getType($name) === 'float32');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  ARRAYS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Registra un array con sus dimensiones.
     * Reserva espacio contiguo en el stack frame.
     *
     * @param string   $name      nombre de la variable
     * @param int[]    $dims      dimensiones, e.g. [5] o [2,3]
     * @param string   $elemType  tipo de elemento: 'int32', 'float32', 'bool', etc.
     * @return int     offset base del primer elemento [fp - base_offset]
     */
    public function allocArray(string $name, array $dims, string $elemType): int
    {
        if (isset($this->arrays[$name])) {
            return $this->arrays[$name]['base_offset'];
        }

        $totalSlots = array_product($dims);  // 2×3 = 6 slots
        $baseOffset = $this->nextOffset;

        $this->arrays[$name] = [
            'base_offset' => $baseOffset,
            'dims'        => $dims,
            'elem_type'   => $elemType,
            'total_slots' => $totalSlots,
        ];

        $this->nextOffset += $totalSlots * 8;  // 8 bytes por slot (alineación)
        return $baseOffset;
    }

    public function hasArray(string $name): bool
    {
        return isset($this->arrays[$name]);
    }

    public function getArrayInfo(string $name): ?array
    {
        return $this->arrays[$name] ?? null;
    }

    /**
     * Calcula el offset de un elemento multidimensional.
     * Para a[i][j] en [2][3]int32:  offset = base + (i*3 + j) * 8
     *
     * @param string $name    nombre del array
     * @param int[]  $indices índices en cada dimensión
     * @return int   offset desde fp (para acceso constante)
     */
    public function getArrayElementOffset(string $name, array $indices): int
    {
        if (!isset($this->arrays[$name])) return 0;
        $info  = $this->arrays[$name];
        $dims  = $info['dims'];
        $flat  = 0;

        // Row-major: flat = i0*d1*d2*... + i1*d2*... + i2*...
        for ($k = 0; $k < count($indices); $k++) {
            $stride = 1;
            for ($j = $k + 1; $j < count($dims); $j++) {
                $stride *= $dims[$j];
            }
            $flat += $indices[$k] * $stride;
        }

        return $info['base_offset'] + $flat * 8;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  CALLEE-SAVED REGISTERS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Registra que esta función usa el registro callee-saved dado.
     * El generador emitirá stp/ldp correspondientes en prólogo/epílogo.
     */
    public function useCalleeSaved(string $reg): void
    {
        if (!in_array($reg, $this->calleeSaved)) {
            $this->calleeSaved[]       = $reg;
            $this->calleeSavedBytes   += 16; // pares de 16 bytes
        }
    }

    public function getCalleeSaved(): array
    {
        return $this->calleeSaved;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  TEMPORALES
    // ═══════════════════════════════════════════════════════════════════════════

    /** Registra el uso de un slot temporal (para cálculo del frame máximo). */
    public function pushTemp(): void
    {
        $this->currentTempDepth++;
        if ($this->currentTempDepth > $this->maxTempDepth) {
            $this->maxTempDepth = $this->currentTempDepth;
        }
    }

    public function popTemp(): void
    {
        if ($this->currentTempDepth > 0) $this->currentTempDepth--;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  TAMAÑO DEL FRAME
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Tamaño total del frame de variables locales, redondeado a múltiplo de 16.
     * Incluye: variables escalares + arrays + espacio para temporales.
     * NO incluye los 16 bytes del stp x29,x30 (gestionados por el stp mismo).
     */
    public function getFrameSize(): int
    {
        $localBytes = $this->nextOffset - 8;  // bytes usados por variables
        if ($localBytes <= 0 && $this->maxTempDepth === 0) {
            return 0;
        }
        // Redondear al múltiplo de 16 más cercano
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

    public function getNextOffset(): int
    {
        return $this->nextOffset;
    }

    /** Lista completa de variables locales (para debug/tabla de símbolos). */
    public function getLocals(): array
    {
        return $this->locals;
    }
}