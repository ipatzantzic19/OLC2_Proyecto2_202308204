<?php

namespace Golampi\Compiler\ARM64\FunctionContext;

/**
 * RegisterManager — Gestión de registros callee-saved
 *
 * Responsabilidad (Fase 2 - Optimización de registros):
 *   Registrar qué registros callee-saved (x19-x28) esta función utiliza
 *   y necesita preservar en prólogo/epílogo.
 *
 * Convención AArch64 (AAPCS64 - ARM Architecture Calling Standard):
 *   - Registros caller-saved: x0-x18, x30 (libre para modificar sin salvar)
 *   - Registros callee-saved: x19-x28 (si los uso, debo restaurarlos)
 *   - x29 (FP) y x30 (LR) son especiales: gestionados por stp/ldp
 *
 * Concepto de compiladores (Aho et al.):
 *   - Asignación de registros: QUÉ registros usar (coloreado de grafos)
 *   - Salvo y restauro: CUÁNDO generar stp/ldp (prólogo/epílogo)
 */
trait RegisterManager
{
    /**
     * Lista de registros callee-saved actualmente en uso por esta función.
     * Ej: ['x19', 'x20'] si usa dos registros para almacenamiento de largo plazo.
     */
    private array $calleeSaved = [];

    /**
     * Bytes reservados en stack para callee-saved (siempre múltiplo de 16).
     * Cada registro = 8 bytes, pero AArch64 alinea pares (stp/ldp) = 16 bytes.
     */
    private int   $calleeSavedBytes = 0;

    /**
     * Registra que esta función utiliza un registro callee-saved.
     * El generador de código emitirá stp/ldp en prólogo/epílogo.
     *
     * Precondición: $reg debe ser válido: x19-x28, sp, fp, lr (excluir si es especial)
     * Postcondición: el registro se marca como "en uso" y se calcula espacio.
     *
     * @param string $reg nombre del registro (ej: 'x19', 'x20')
     */
    public function useCalleeSaved(string $reg): void
    {
        if (!in_array($reg, $this->calleeSaved)) {
            $this->calleeSaved[] = $reg;
            // AArch64: registros en pares (stp guarda 2 registros = 16 bytes)
            // Contar como medio registro (se emparea en prólogo)
            $this->calleeSavedBytes += 16;
        }
    }

    /**
     * Obtiene el listado de registros callee-saved en uso.
     * @return array nombres de registros ej: ['x19', 'x20', 'x21']
     */
    public function getCalleeSaved(): array
    {
        return $this->calleeSaved;
    }

    /**
     * Verifica si un registro específico está en uso.
     */
    public function usesCalleeSaved(string $reg): bool
    {
        return in_array($reg, $this->calleeSaved);
    }

    /**
     * Obtiene el número de registros callee-saved en uso.
     */
    public function getCalleeSavedCount(): int
    {
        return count($this->calleeSaved);
    }

    /**
     * Obtiene el número de bytes necesarios para salvar callee-saved.
     * @return int múltiplo de 16 (alineación AArch64)
     */
    public function getCalleeSavedBytes(): int
    {
        return $this->calleeSavedBytes;
    }

    /**
     * Verifica si la función necesita salvar registros.
     * @return bool true si hay registros callee-saved en uso
     */
    public function hasCalleeSaved(): bool
    {
        return count($this->calleeSaved) > 0;
    }

    /**
     * Genera el código de prólogo para salvar callee-saved.
     * Formato ARM64: stp x19, x20, [sp, #-16]!  (decrementa sp y guarda)
     *
     * @return string[] líneas de assembly para el prólogo
     */
    public function generateCalleeSavedProlog(): array
    {
        $prolog = [];
        for ($i = 0; $i < count($this->calleeSaved); $i += 2) {
            $reg1 = $this->calleeSaved[$i];
            $reg2 = isset($this->calleeSaved[$i + 1]) 
                ? $this->calleeSaved[$i + 1] 
                : 'xzr';  // Si sobra uno, emparejar con xzr
            $prolog[] = "stp $reg1, $reg2, [sp, #-16]!";
        }
        return $prolog;
    }

    /**
     * Genera el código de epílogo para restaurar callee-saved.
     * Inverso del prólogo: ldp reg1, reg2, [sp], #16  (carga e incrementa sp)
     *
     * @return string[] líneas de assembly para el epílogo (en orden inverso)
     */
    public function generateCalleeSavedEpilog(): array
    {
        $epilog = [];
        // Restaurar en orden inverso (LIFO)
        for ($i = count($this->calleeSaved) - 2; $i >= 0; $i -= 2) {
            $reg1 = $this->calleeSaved[$i];
            $reg2 = isset($this->calleeSaved[$i + 1])
                ? $this->calleeSaved[$i + 1]
                : 'xzr';
            $epilog[] = "ldp $reg1, $reg2, [sp], #16";
        }
        return $epilog;
    }
}
