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
            $this->errors[] = [
                'type'        => 'Fatal',
                'description' => 'No se encontró la función main()',
                'line'        => 0,
                'column'      => 0,
            ];
            return new \Golampi\Compiler\CompilationResult('', $this->errors, $this->symbolTable);
        }

        $mainFunc = $this->userFunctions['main'];
        $mainCtx = new \Golampi\Compiler\ARM64\FunctionContext('main');

        $this->phaseGenerateFunction('main', $mainFunc['ctx'], $mainCtx);
        
        // Guardar contexto para tabla de símbolos
        if (!isset($this->userFunctionContexts)) {
            $this->userFunctionContexts = [];
        }
        $this->userFunctionContexts['main'] = $mainCtx;

        // Fase 3: Generar código para funciones de usuario
        foreach ($this->userFunctions as $name => $funcInfo) {
            if ($name === 'main') continue; // Ya generada

            $funcCtx = new \Golampi\Compiler\ARM64\FunctionContext($name);
            $this->phaseGenerateFunction($name, $funcInfo['ctx'], $funcCtx);
            
            // Guardar contexto para tabla de símbolos
            $this->userFunctionContexts[$name] = $funcCtx;
        }

        // Fase 4: Generar tabla de símbolos para depuración
        $this->phaseGenerateSymbolTable();

        // Fase 5: Generar helpers runtime si se usaron
        $this->phaseGenerateHelpers();

        // Fase 6: Construir assembly final
        $assembly = $this->buildAssembly();

        return new \Golampi\Compiler\CompilationResult($assembly, $this->errors, $this->symbolTable);
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

            $fullClass = get_class($child);
            $class = substr($fullClass, strrpos($fullClass, '\\') + 1);
            
            // Buscar función en DeclarationContext o directamente en FunctionDeclContext
            if ($class === 'DeclarationContext') {
                // Buscar función dentro de Declaration
                for ($j = 0; $j < $child->getChildCount(); $j++) {
                    $decChild = $child->getChild($j);
                    if ($decChild instanceof \TerminalNode) continue;
                    
                    $funcDecl = $this->extractFuncFromContext($decChild);
                    if ($funcDecl !== null) {
                        $this->registerUserFunction($funcDecl);
                        break;
                    }
                }
            } elseif ($class === 'FunctionDeclContext') {
                $this->registerUserFunction($child);
            }
        }
    }

    /**
     * Extrae una declaración de función de un contexto.
     * @return object|null FlatUserFunction context o null
     */
    private function extractFuncFromContext($ctx): ?object
    {
        try {
            if (is_callable([$ctx, 'functionDeclaration'])) {
                $fd = $ctx->functionDeclaration();
                if ($fd !== null) return $fd;
            }
            // Si el contexto ya es una función (tiene ID y block)
            if (is_callable([$ctx, 'ID']) && is_callable([$ctx, 'block'])) {
                return $ctx;
            }
        } catch (\Throwable $e) {}
        return null;
    }

    /**
     * Registra una declaración de función en $this->userFunctions.
     * Se llama durante prescan global.
     *
     * @param object $funcDeclCtx FunctionDeclContext o FuncDeclSingleReturnContext
     */
    protected function registerUserFunction($funcDeclCtx): void
    {
        // Intentar extraer nombre de identifier() (FunctionDeclContext)
        $name = null;
        if (method_exists($funcDeclCtx, 'identifier')) {
            try {
                $nameCtx = $funcDeclCtx->identifier();
                $name = $nameCtx->getText();
            } catch (\Throwable $e) {}
        }
        
        // Si no, intentar extraer de ID() (FuncDeclSingleReturnContext)
        if ($name === null && is_callable([$funcDeclCtx, 'ID'])) {
            try {
                $idToken = $funcDeclCtx->ID();
                $name = $idToken->getText();
            } catch (\Throwable $e) {}
        }
        
        // Si no se pudo extraer nombre, salir
        if ($name === null) {
            return;
        }

        // Extraer bloque (body)
        $blockCtx = null;
        if (method_exists($funcDeclCtx, 'block')) {
            try {
                $blockCtx = $funcDeclCtx->block();
            } catch (\Throwable $e) {}
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
