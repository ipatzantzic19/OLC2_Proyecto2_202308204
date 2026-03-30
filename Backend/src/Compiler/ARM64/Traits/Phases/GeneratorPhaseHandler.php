<?php

namespace Golampi\Compiler\ARM64\Traits\Phases;

/**
 * GeneratorPhaseHandler — Orquestador de fases de compilación
 *
 * ARQUITECTURA DE FASES (conforme a Aho et al. "Compiladores"):
 *
 *   PrescanPhase        → Fase preliminar: descubrimiento de símbolos
 *   GenerationPhase     → Fase principal: síntesis de código ARM64
 *   ProgramPhase        → Fase integración: estructura del programa completo
 *
 * Beneficios de esta estructura:
 *   ✓ Cada fase tiene responsabilidad única
 *   ✓ Fácil de testear por separado
 *   ✓ Alineado con compiladores reales (gcc, llvm)
 *   ✓ Facilita depuración (agregar printfs por fase)
 *   ✓ Preparado para optimizaciones futuras (intermediate representation, etc.)
 *
 * Flujo:
 *   compile(ast)
 *     └─┬─ prescanGlobalFunctions()    [PrescanPhase]
 *       ├─ prescanBlock()               [PrescanPhase]
 *       ├─ generateFunction(main)       [GenerationPhase]
 *       │  └─ generatePrologue/Block/Epilogue
 *       ├─ generateFunction(user_fn)    [GenerationPhase]
 *       ├─ generateSymbolTable()        [GenerationPhase]
 *       ├─ generateHelpers()            [ProgramPhase]
 *       └─ buildAssembly()              [EmitterHandler]
 */
trait GeneratorPhaseHandler
{
    // Importar todos los traits de fase
    use PrescanPhase;
    use GenerationPhase;
    use ProgramPhase;
}
