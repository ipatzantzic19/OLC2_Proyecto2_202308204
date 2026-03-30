<?php

namespace Golampi\Compiler\ARM64\Traits\Phases;

/**
 * ProgramPhase — Orquestación de la compilación completa del programa
 *
 * Responsabilidad:
 *   Coordinar todas las fases de compilación para el programa completo:
 *   1. Prescan global: registrar todas las funciones de usuario (hoisting)
 *   2. Generar código para main
 *   3. Generar código para funciones de usuario
 *   4. Generar helpers runtime
 *   5. Construir salida final
 *
 * Concepto de compiladores (Aho et al.):
 *   Estructura de programa: sintaxis y semántica de nivel superior
 *   (declaraciones de función, scope global, etc.)
 */
trait ProgramPhase
{
    use PrescanPhase;
    use GenerationPhase;

    /**
     * Compila el programa completo: prescan global + generación + síntesis (versión fase).
     *
     * Reemplaza al método compile() anterior.
     *
     * @param object $programCtx AST raíz (ProgramContext)
     * @return CompilationResult resultado con assembly o errores
     */
    protected function phaseCompileProgram($programCtx): object
    {
        // Fase 1: Prescan global de funciones (hoisting)
        $this->phasePrescanGlobalFunctions($programCtx);

        // Fase 2: Generar código para main
        if (!isset($this->userFunctions['main'])) {
            $this->errors[] = 'Error: función main no encontrada';
            return new CompilationResult(null, $this->errors, $this->symbolTable);
        }

        $mainFunc = $this->userFunctions['main'];
        $mainCtx = new FunctionContext('main');

        $this->phaseGenerateFunction('main', $mainFunc['block'], $mainCtx);

        // Fase 3: Generar código para funciones de usuario
        foreach ($this->userFunctions as $name => $funcInfo) {
            if ($name === 'main') continue; // Ya generada

            $funcCtx = new FunctionContext($name);
            $this->phaseGenerateFunction($name, $funcInfo['block'], $funcCtx);
        }

        // Fase 4: Generar tabla de símbolos para depuración
        $this->phaseGenerateSymbolTable();

        // Fase 5: Generar helpers runtime si se usaron
        $this->phaseGenerateHelpers();

        // Fase 6: Construir assembly final
        $assembly = $this->buildAssembly();

        return new CompilationResult($assembly, $this->errors, $this->symbolTable);
    }

    /**
     * Prescan global: identifica todas las funciones de usuario (versión fase).
     * Útil para hoisting (permitir llamar función antes de ser declarada).
     *
     * @param object $programCtx AST raíz
     */
    protected function phasePrescanGlobalFunctions($programCtx): void
    {
        if (!isset($programCtx)) return;

        $children = $programCtx->getChildCount();
        for ($i = 0; $i < $children; $i++) {
            $child = $programCtx->getChild($i);

            if ($child instanceof \TerminalNode) {
                continue;
            }

            $class = class_basename($child);
            if ($class === 'FunctionDeclContext') {
                $this->registerUserFunction($child);
            }
        }
    }

    /**
     * Registra una declaración de función en $this->userFunctions.
     * Se llama durante prescan global.
     *
     * @param object $funcDeclCtx FunctionDeclContext
     */
    protected function registerUserFunction($funcDeclCtx): void
    {
        if (!method_exists($funcDeclCtx, 'identifier')) {
            return;
        }

        $nameCtx = $funcDeclCtx->identifier();
        $name = $nameCtx->getText();

        // Extraer bloque (body)
        $blockCtx = null;
        if (method_exists($funcDeclCtx, 'block')) {
            $blockCtx = $funcDeclCtx->block();
        }

        // Registrar
        $this->userFunctions[$name] = [
            'name'  => $name,
            'block' => $blockCtx,
            'ctx'   => $funcDeclCtx,
        ];
    }

    /**
     * Genera funciones helper runtime (golampi_concat, golampi_substr, golampi_now) - versión fase.
     * Se llaman durante la ejecución de operaciones especiales.
     *
     * Precondición: Los handlers (StringOpsHandler, etc.) han marcado qué helpers usar.
     * Postcondición: Instrucciones de helpers agregadas a $this->postTextLines.
     */
    protected function phaseGenerateHelpers(): void
    {
        // Generar golampi_concat si se usó emitStringConcat()
        if (isset($this->generatedHelpers['concat'])) {
            $this->generateHelperConcat();
        }

        // Generar golampi_substr si se usó emitSubstr()
        if (isset($this->generatedHelpers['substr'])) {
            $this->generateHelperSubstr();
        }

        // Generar golampi_now si se usó emitNow()
        if (isset($this->generatedHelpers['now'])) {
            $this->generateHelperNow();
        }
    }

    /**
     * Obtiene lista de errores de compilación.
     *
     * @return array errores registrados
     */
    protected function phaseGetCompilationErrors(): array
    {
        return $this->errors;
    }

    /**
     * Obtiene tabla de símbolos para depuración/análisis.
     *
     * @return array tabla de símbolos
     */
    protected function phaseGetSymbolTable(): array
    {
        return $this->symbolTable;
    }
}
