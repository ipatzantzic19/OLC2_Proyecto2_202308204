<?php

namespace Golampi\Compiler\ARM64\FunctionContext;

/**
 * ArrayManager — Gestión de arrays multidimensionales
 *
 * Responsabilidad (Fase 2 - Soporte para arrays):
 *   Registrar arrays multidimensionales y calcular offsets de elementos
 *   usando indexación row-major (estándar en C y compiladores típicos).
 *
 * Modelo de almacenamiento (AArch64):
 *   - Arrays viven en el stack frame, contiguo a variables locales.
 *   - Se reserva espacio: totalSlots × 8 bytes (alineación)
 *   - Indexación row-major: a[i][j] en dims [2][3] → offset = (i*3 + j)*8
 *
 * Conceptos de compiladores (Aho et al.):
 *   - Cada dimensión = multiplicador para flatten del índice
 *   - El descriptor almacena las dimensiones para validar acceso en tiempo de compilación
 */
trait ArrayManager
{
    /**
     * Tabla de arrays registrados en el frame.
     * Clave: nombre del array.
     * Valor: ['base_offset'=>int, 'dims'=>int[], 'elem_type'=>string, 'total_slots'=>int]
     */
    private array $arrays = [];

    /**
     * Registra un array multidimensional en el stack frame.
     * Reserva espacio contiguo y retorna el offset base.
     *
     * Precondición: $name no debe estar registrado previamente.
     *              Llamada típicamente desde Prescan.
     * Postcondición: el array está ubicado de $baseOffset en adelante.
     *
     * @param string $name       nombre del array (ej: "matrix")
     * @param int[]  $dims       dimensiones de cada eje (ej: [2, 3] para 2×3)
     * @param string $elemType   tipo de elemento: 'int32', 'float32', 'bool', etc.
     * @return int   offset base desde fp: [fp - $baseOffset]
     */
    public function allocArray(string $name, array $dims, string $elemType): int
    {
        if (isset($this->arrays[$name])) {
            // Si ya existe, devolver su offset actual
            return $this->arrays[$name]['base_offset'];
        }

        // Calcular cantidad total de elementos
        $totalSlots = array_product($dims);  // [2,3] → 6 elementos

        // Ubicar array en el siguiente offset disponible
        $baseOffset = $this->getNextOffset();

        $this->arrays[$name] = [
            'base_offset' => $baseOffset,
            'dims'        => $dims,
            'elem_type'   => $elemType,
            'total_slots' => $totalSlots,
        ];

        // Reservar espacio: 8 bytes por slot (alineación AArch64)
        $this->advanceOffset($totalSlots * 8);

        return $baseOffset;
    }

    /**
     * Verifica si un nombre refiere a un array registrado.
     */
    public function hasArray(string $name): bool
    {
        return isset($this->arrays[$name]);
    }

    /**
     * Obtiene el descriptor completo de un array.
     * @return array|null descriptor con base_offset, dims, elem_type, total_slots
     */
    public function getArrayInfo(string $name): ?array
    {
        return $this->arrays[$name] ?? null;
    }

    /**
     * Calcula el offset de un elemento específico usando row-major indexing.
     *
     * Algoritmo (Aho et al., "Compiladores"):
     *   Para array a de dims [d0][d1][d2]... en dirección base:
     *   offset(i0, i1, i2, ...) = (i0*d1*d2*... + i1*d2*... + i2*... + ...) * 8
     *
     * Ejemplo: a[1][2] en dims [3][4]:
     *   offset = (1*4 + 2) * 8 = (4+2) * 8 = 48 bytes desde base
     *
     * @param string $name    nombre del array
     * @param int[]  $indices índices en cada dimensión [i0, i1, i2, ...]
     * @return int   offset total desde fp
     */
    public function getArrayElementOffset(string $name, array $indices): int
    {
        if (!isset($this->arrays[$name])) {
            return 0;
        }

        $info = $this->arrays[$name];
        $dims = $info['dims'];
        $flat = 0;

        // Row-major flatten: suma ponderada con strides
        for ($k = 0; $k < count($indices); $k++) {
            $stride = 1;
            // Calcular stride: producto de todas las dimensiones siguientes
            for ($j = $k + 1; $j < count($dims); $j++) {
                $stride *= $dims[$j];
            }
            $flat += $indices[$k] * $stride;
        }

        // Offset total = base + flat*8  (elementos de 8 bytes)
        return $info['base_offset'] + $flat * 8;
    }

    /**
     * Obtiene el tipo de elemento de un array.
     * Usado en emisión de instrucciones de carga (ldr vs ldr-s).
     *
     * @param string $name nombre del array
     * @return string tipo de elemento o 'int32' por defecto
     */
    public function getArrayElemType(string $name): string
    {
        return $this->arrays[$name]['elem_type'] ?? 'int32';
    }

    /**
     * Verifica si los elementos de un array son float32.
     */
    public function isArrayFloat(string $name): bool
    {
        return ($this->getArrayElemType($name) === 'float32');
    }

    /**
     * Obtiene las dimensiones de un array.
     * @param string $name nombre del array
     * @return int[] dimensiones registradas
     */
    public function getArrayDims(string $name): array
    {
        return $this->arrays[$name]['dims'] ?? [];
    }

    /**
     * Verifica cuántos elementos totales tiene el array.
     * Útil para validación en tiempo de compilación.
     */
    public function getArrayTotalSlots(string $name): int
    {
        return $this->arrays[$name]['total_slots'] ?? 0;
    }

    /**
     * Obtiene la tabla completa de arrays registrados.
     * @return array hash name → descriptor array info
     */
    public function getArrays(): array
    {
        return $this->arrays;
    }

    /**
     * Calcula el tamaño total en bytes de todos los arrays.
     */
    public function getArraysSizeBytes(): int
    {
        $totalBytes = 0;
        foreach ($this->arrays as $info) {
            $totalBytes += $info['total_slots'] * 8;
        }
        return $totalBytes;
    }
}
