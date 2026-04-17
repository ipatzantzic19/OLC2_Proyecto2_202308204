<?php

namespace Golampi\Compiler\ARM64\FunctionContext;

/**
 * LocalsManager — Gestión de variables locales escalares
 *
 * Responsabilidad (Fase 1 - Análisis semántico):
 *   Registrar y ubicar todas las variables escalares (int32, float32, bool, string, rune)
 *   en el stack frame de la función.
 *
 * Modelo de memoria (AArch64):
 *   Cada variable ocupa exactamente 8 bytes (incluso float32 se alinea a 8 bytes).
 *   Se almacenan en orden LIFO conforme se declaran: var1 [fp-8], var2 [fp-16], etc.
 *
 * Estado que gestiona:
 *   - $locals: hash name → {offset, type, is_param}
 *   - $nextOffset: contador para ubicar próxima variable
 */
trait LocalsManager
{
    /**
     * Hash de variables locales escalares.
     * Clave: nombre de variable.
     * Valor: ['offset' => int, 'type' => string, 'is_param' => bool]
     *
     * Tipos soportados: 'int32', 'float32', 'bool', 'string', 'rune', 'nil', 'unknown'
     */
    private array $locals     = [];

    /**
     * Offset del siguiente slot disponible (contado desde [fp - 8]).
     * Se incrementa de 8 en 8 (una variable por slot = 8 bytes).
     */
    private int   $nextOffset = 8;

    /**
     * Registra una variable escalar en el stack frame.
     * Devuelve el offset desde fp donde se almacena.
     *
     * Precondición: $name debe ser un identificador válido.
     * Postcondición: la variable está en $locals y ubicada en stack.
     *
     * @param string $name    nombre de la variable
     * @param string $type    tipo: 'int32', 'float32', 'bool', 'string', 'rune', 'nil'
     * @param bool   $isParam true si es parámetro formal (copiado desde registros x0-x7)
     * @return int   offset desde fp: [fp - $offset]
     */
    public function allocLocal(string $name, string $type, bool $isParam = false): int
    {
        if (isset($this->locals[$name])) {
            // Variable ya existe: actualizar tipo si era 'unknown'
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
        $this->nextOffset += 8;  // Siguiente slot
        return $offset;
    }

    /**
     * Verifica si una variable local existe (no confundir con arrays).
     * Devuelve true solo para variables escalares.
     */
    public function hasLocal(string $name): bool
    {
        return isset($this->locals[$name]);
    }

    /**
     * Obtiene el offset de una variable local desde fp.
     * @return int offset positivo (ej: 8, 16, 24, ...)
     */
    public function getLocalOffset(string $name): int
    {
        return $this->locals[$name]['offset'] ?? 0;
    }

    /**
     * Obtiene el tipo de una variable local.
     * @return string tipo registrado o 'int32' por defecto
     */
    public function getLocalType(string $name): string
    {
        return $this->locals[$name]['type'] ?? 'int32';
    }

    /**
     * Establece el tipo de una variable (para inferencia de tipos).
     * Solo lo hace si la variable no tiene tipo conocido.
     */
    public function setLocalType(string $name, string $type): void
    {
        if (isset($this->locals[$name]) && $type !== 'unknown') {
            $this->locals[$name]['type'] = $type;
        }
    }

    /**
     * Verifica si una variable es de tipo float32.
     * Útil para emitir ldr-s vs ldr en acceso.
     */
    public function isLocalFloat(string $name): bool
    {
        return ($this->getLocalType($name) === 'float32');
    }

    /**
     * Obtiene el número de variables locales escalares registradas.
     */
    public function getLocalCount(): int
    {
        return count($this->locals);
    }

    /**
     * Obtiene la lista completa de variables locales.
     * Útil para tabla de símbolos y debug.
     * @return array hash name → descriptor
     */
    public function getLocals(): array
    {
        return $this->locals;
    }

    /**
     * Calcula cuántos bytes ocupan todas las variables locales.
     * Incluye solo variables escalares (arrays se gestiona aparte).
     * @return int bytes desde [fp-8] hasta [fp-nextOffset]
     */
    public function getLocalsSizeBytes(): int
    {
        return $this->nextOffset - 8;
    }

    /**
     * Obtiene el offset del siguiente slot disponible.
     * Integradores (como ArrayManager) consultan esto para ubicar arrays.
     */
    public function getNextOffset(): int
    {
        return $this->nextOffset;
    }

    /**
     * Avanza el contador de offsets (para que ArrayManager reserve espacio).
     * Se llama cuando un array necesita espacio.
     *
     * @param int $bytes cantidad de bytes a reservar
     */
    protected function advanceOffset(int $bytes): void
    {
        $this->nextOffset += $bytes;
    }

    /**
     * Verifica si el frame alcanzó el límite de tamaño máximo.
     * Límite: MAX_FRAME (2048 bytes en Fase 2).
     */
    protected function isLocalsOverflow(): bool
    {
        return ($this->nextOffset - 8) >= 2048;
    }
}
