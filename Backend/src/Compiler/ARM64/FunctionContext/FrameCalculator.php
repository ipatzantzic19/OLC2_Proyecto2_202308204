<?php

namespace Golampi\Compiler\ARM64\FunctionContext;

/**
 * FrameCalculator — Cálculo de tamaño del stack frame
 *
 * Responsabilidad (Fase 1 y 2 - Asignación de memoria):
 *   Calcular el tamaño total del stack frame combinando variables locales,
 *   arrays, callee-saved registers y espacio para temporales.
 *
 * Diseño conforme a Aho et al. ("Compiladores: Principios, Técnicas y Herramientas"):
 *   Frame layout en AArch64:
 *
 *   [dirección alta]
 *   ┌─────────────────────┐ ← sp_original
 *   │ x29 (FP saved)      │ ← [fp + 0]     (16 bytes stp x29, x30)
 *   │ x30 (LR saved)      │ ← [fp + 8]
 *   ├─────────────────────┤ ← x29 = fp (frame pointer)
 *   │ callee-saved (si)   │ ← [fp - 8 .. fp - CSB]
 *   ├─────────────────────┤
 *   │ variables + arrays  │ ← [fp - 8 .. fp - SIZE]
 *   ├─────────────────────┤
 *   │ temporales (dyn)    │ ← sub sp, sp, #TEMPSIZE
 *   └─────────────────────┘ ← sp (durante ejecución)
 *   [dirección baja]
 *
 * Nota: El stp x29, x30 NO es parte del FRAME_SIZE (es preámbulo).
 *       FRAME_SIZE = variables + arrays + callee-saved, alineado a 16.
 */
trait FrameCalculator
{
    /**
     * Límite máximo de tamaño de frame (en bytes).
     * Fase 1: 256 bytes (32 variables).
     * Fase 2: 2048 bytes (más variables y arrays medianos).
     */
    const MAX_FRAME = 2048;

    /**
     * Espacio máximo usado por temporales en stack durante evaluación de expresiones.
     * Se recalcula en cada llamada a emitir temporales.
     * Usado en prescan para reservar el espacio máximo necesario.
     */
    private int $maxTempDepth = 0;

    /**
     * Profundidad actual de temporales (se incrementa en pushStack/decrece en pop).
     * Usado para recalcular maxTempDepth durante generación de código.
     */
    private int $currentTempDepth = 0;

    /**
     * Calcula el tamaño TOTAL del stack frame de esta función.
     *
     * Algoritmo:
     *   1. localBytes = diferencia entre offsets de variables locales+arrays
     *   2. caileeSavedBytes = registros preservados (múltiplo de 16)
     *   3. frameSize = localBytes + calleeSavedBytes + temporalsSpaceNeeded
     *   4. Redondear al múltiplo de 16 más cercano (alineación AArch64)
     *
     * Retorna: bytes a restar de sp en el prólogo (sub sp, sp, #frameSize)
     *
     * @return int tamaño alineado a 16 bytes, o 0 si no hay locals
     */
    public function calculateFrameSize(): int
    {
        // Calcular bytes para variables locales + arrays
        $localBytes = $this->getLocalsSizeBytes() + $this->getArraysSizeBytes();

        // Obtener bytes para callee-saved (ya múltiplo de 16)
        $calleeSavedBytes = $this->getCalleeSavedBytes();

        // Sumar espacio para temporales (worst-case)
        $tempBytes = $this->maxTempDepth * 8;

        // Total
        $totalUnaligned = $localBytes + $calleeSavedBytes + $tempBytes;

        if ($totalUnaligned <= 0) {
            return 0;
        }

        // Redondear al múltiplo de 16 más cercano (AArch64 alineación)
        return (int)(ceil($totalUnaligned / 16) * 16);
    }

    /**
     * Obtiene el tamaño del frame (alias para calculateFrameSize).
     * Se llama desde el generador para emitir instrucciones de prólogo.
     *
     * @return int tamaño alineado
     */
    public function getFrameSize(): int
    {
        return $this->calculateFrameSize();
    }

    /**
     * Verifica que el frame no haya excedido el límite.
     * Se llama tras prescan para validar límites.
     *
     * @return bool true si está dentro de límites
     */
    public function isFrameValid(): bool
    {
        return ($this->getLocalsSizeBytes() + $this->getArraysSizeBytes()) <= self::MAX_FRAME;
    }

    /**
     * Verifica si el frame está aún pequeño (optimización).
     * Si es muy grande, puede ayudar en decisiones de emisión de código.
     */
    public function isFrameFull(): bool
    {
        return ($this->getLocalsSizeBytes() + $this->getArraysSizeBytes()) >= self::MAX_FRAME;
    }

    /**
     * Obtiene el límite máximo de frame soportado en esta instancia.
     * Útil para mensajes de error.
     */
    public function getMaxFrameSize(): int
    {
        return self::MAX_FRAME;
    }

    /**
     * Registra el uso de un slot temporal (para push al stack).
     * Incrementa la profundidad actual y recalcula máximo.
     * Se llama desde EmitterHandler::pushStack()
     */
    public function pushTemp(): void
    {
        $this->currentTempDepth++;
        if ($this->currentTempDepth > $this->maxTempDepth) {
            $this->maxTempDepth = $this->currentTempDepth;
        }
    }

    /**
     * Libera un slot temporal (para pop del stack).
     * Decrementa la profundidad actual.
     * Se llama desde EmitterHandler::popStack()
     */
    public function popTemp(): void
    {
        if ($this->currentTempDepth > 0) {
            $this->currentTempDepth--;
        }
    }

    /**
     * Obtiene la profundidad máxima de temporales registrada.
     * Útil para debug y validación de stack.
     */
    public function getMaxTempDepth(): int
    {
        return $this->maxTempDepth;
    }

    /**
     * Obtiene la profundidad actual de temporales (durante emisión).
     */
    public function getCurrentTempDepth(): int
    {
        return $this->currentTempDepth;
    }

    /**
     * Reseta máximos de temporales para la próxima función.
     * Se llama al cambiar de contexto de función.
     */
    public function resetTempDepths(): void
    {
        $this->maxTempDepth = 0;
        $this->currentTempDepth = 0;
    }

    /**
     * Obtiene un resumen del cálculo de frame (para debug).
     * @return array desglose por componente
     */
    public function getFrameSummary(): array
    {
        return [
            'locals'      => $this->getLocalsSizeBytes(),
            'arrays'      => $this->getArraysSizeBytes(),
            'calleeSaved' => $this->getCalleeSavedBytes(),
            'temps'       => $this->maxTempDepth * 8,
            'total'       => $this->getFrameSize(),
        ];
    }
}
