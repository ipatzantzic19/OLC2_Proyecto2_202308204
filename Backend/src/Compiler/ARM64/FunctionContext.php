<?php

namespace Golampi\Compiler\ARM64;

use Golampi\Compiler\ARM64\FunctionContext\FunctionContextHandler;

require_once __DIR__ . '/FunctionContext/FunctionContextHandler.php';

/**
 * FunctionContext — Registro de activación de una función Golampi (refactorizado)
 *
 * VERSIÓN REFACTORIZADA: Separa responsabilidades mediante traits especializados
 *
 * Este es el punto de entrada público para gestionar el contexto de una función.
 * Coordina el modelo de memoria completo en AArch64 (AAPCS64 convention):
 *
 *  [dirección alta]
 *  ┌──────────────────────┐ ← sp_original (antes del stp)
 *  │  x29 (saved FP)      │ ← [fp + 0]    stp x29,x30,[sp,#-16]!
 *  │  x30 (saved LR)      │ ← [fp + 8]
 *  ├──────────────────────┤ ← x29 = fp (frame pointer / enlace de control)
 *  │  callee-saved regs   │ ← [fp - 8 .. fp - N*8]   si se usan x19-x28
 *  ├──────────────────────┤
 *  │  parámetros locales  │ (copiados desde x0-x7 / s0-s7 al inicio)
 *  │  variables locales   │ cada una 8 bytes alineados
 *  ├──────────────────────┤
 *  │  float locals (32b)  │ almacenados en 8 bytes por alineación
 *  ├──────────────────────┤
 *  │  temporales expr.    │ espacio reservado para expresiones complejas
 *  └──────────────────────┘ ← sp (fp - FRAME_SIZE)
 *  [dirección baja]
 *
 * Modelo de información (compiladores Aho et al.):
 *   - Descriptor de variable: offset en stack + tipo + esparámetro?
 *   - Tipo del slot: int32 vs float32 → instrucción de carga correcta
 *   - Array descriptor: base + dimensiones + tipo elemento → cálculo de índice
 *
 * Refactorización en managers especializados:
 *   ✓ LocalsManager     → Variables escalares en stack
 *   ✓ ArrayManager      → Arrays multidimensionales (row-major indexing)
 *   ✓ RegisterManager   → Callee-saved Register management
 *   ✓ FrameCalculator   → Cálculo integral del frame size
 *
 * Beneficios de esta arquitectura:
 *   - Cada manager tiene una responsabilidad única (SOLID)
 *   - Fácil de testear por separado
 *   - Menor acoplamiento vs versión monolítica anterior
 *   - Extensible: agregar nuevo tipo de persistencia = agregar nuevo manager
 *   - Conceptualmente alineado con compiladores reales (Aho et al.)
 */
class FunctionContext
{
    // Usar todos los managers como traits
    use FunctionContextHandler;

    /**
     * Nombre de la función (ej: "main", "sumar", "printMatrix")
     */
    public string $name;

    /**
     * Etiqueta del epílogo (ej: ".LfnEnd_main")
     * Se genera en GeneratorHandler::generateFunction()
     */
    public string $epilogueLabel = '';

    /**
     * Retornos múltiples: tipos de valores que retorna esta función
     * Ej: ['int32', 'bool'] para "func f() (int32, bool)"
     */
    public array  $returnTypes = [];

    /**
     * Constructor: inicializa un nuevo contexto de función.
     *
     * @param string $name nombre de la función
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * API pública de compatibilidad: si se puede registrar más variables.
     */
    public function canAddMore(): bool
    {
        return !$this->isFrameValid();
    }

    /**
     * API pública: obtiene un resumen completo del frame (debug).
     */
    public function getDiagnostics(): array
    {
        return [
            'name'               => $this->name,
            'locals'             => $this->getLocals(),
            'arrays'             => $this->getArrays(),
            'calleeSaved'        => $this->getCalleeSaved(),
            'frame_summary'      => $this->getFrameSummary(),
            'frameSize'          => $this->getFrameSize(),
            'valid'              => $this->isFrameValid(),
        ];
    }

    /**
     * Obtiene el offset de una variable (local o array).
     * Método de compatibilidad que delega a LocalsManager o ArrayManager.
     */
    public function getOffset(string $name): int
    {
        if ($this->hasLocal($name)) {
            return $this->getLocalOffset($name);
        }
        if ($this->hasArray($name)) {
            $info = $this->getArrayInfo($name);
            return $info['base_offset'] ?? 0;
        }
        return 0;
    }

    /**
     * Establece el tipo de una variable local.
     * Método de compatibilidad que delega a LocalsManager.
     */
    public function setType(string $name, string $type): void
    {
        $this->setLocalType($name, $type);
    }

    /**
     * Obtiene el tipo de una variable local.
     * Método de compatibilidad que delega a LocalsManager.
     */
    public function getType(string $name): string
    {
        if ($this->hasLocal($name)) {
            return $this->getLocalType($name);
        }
        if ($this->hasArray($name)) {
            return 'array';
        }
        return 'int32';
    }

    /**
     * Verifica si una variable es de tipo float32.
     * Método de compatibilidad que delega a LocalsManager.
     */
    public function isFloat(string $name): bool
    {
        return $this->isLocalFloat($name);
    }

    /**
     * Obtiene el número de variables locales escalares registradas.
     * Método de compatibilidad que viene de LocalsManager trait.
     */
    public function getLocalCountValue(): int
    {
        return count($this->getLocals());
    }

    /**
     * Limitador de frame para validación.
     */
    const MAX_FRAME = 2048;

    /**
     * Verifica si el frame está lleno.
     */
    public function isFrameFull(): bool
    {
        return ($this->getLocalsSizeBytes()) >= self::MAX_FRAME;
    }
}