<?php

namespace Golampi\Compiler\ARM64\Traits\Phases;

/**
 * GenerationPhase — Pasada 2: Generación de código ARM64 (paso principal)
 *
 * Responsabilidad (Fase 2 - Síntesis de código):
 *   Ejecutar el prescan, luego recorrer el AST nuevamente generando 
 *   instrucciones ARM64 usando todos los handlers (EmitterHandler, 
 *   ExpressionsHandler, ControlFlowHandler, etc.).
 *
 * Concepto de compiladores (Aho et al.):
 *   Síntesis (generación de código): crear instrucciones usando tabla de símbolos
 *   y descriptores registrados en prescan.
 *
 * Flujo:
 *   1. Prescan: registrar variables
 *   2. Generate prólogo: stp x29, x30; mov fp, sp
 *   3. Generar código del body
 *   4. Generar epílogo: ldp x29, x30; ret
 */
trait GenerationPhase
{
    /**
     * Genera código para un bloque de sentencias (versión de fase).
     * Recorre el AST del bloque visitando cada sentencia.
     *
     * Precondición: $this->func debe estar inicializado y prescan ejecutado.
     * Postcondición: instrucciones ARM64 están en $this->textLines.
     *
     * @param object $blockCtx contexto del bloque
     */
    protected function phaseGenerateBlock($blockCtx): void
    {
        if (!isset($blockCtx)) return;

        $children = $blockCtx->getChildCount();
        for ($i = 0; $i < $children; $i++) {
            $child = $blockCtx->getChild($i);

            // Saltar terminales
            if ($child instanceof \TerminalNode) {
                continue;
            }

            // Usar el visitor patrón para delegar
            if (method_exists($child, 'accept')) {
                $child->accept($this);
            }
        }
    }

    /**
     * Genera prólogo de función: guardar registros y reservar stack frame (versión fase).
     *
     * Secuencia:
     *   stp x29, x30, [sp, #-16]!   // guardar FP y LR, decrementar sp
     *   mov x29, sp                  // establecer FP en nueva posición
     *   [callee-saved prologue]      // si necesita callee-saved
     *   sub sp, sp, #FRAME_SIZE      // reservar frame para locals/arrays
     *
     * @param string $label etiqueta de función (ej: "main", "sumar")
     */
    protected function phaseGeneratePrologue(string $label): void
    {
        if (!isset($this->func)) return;

        // Etiqueta de función
        $this->label($label);

        // BARE-METAL: Si es _start, omitir prologue
        if ($label === '_start') {
            return;
        }

        // Guardar x29 (FP) y x30 (LR) con decremento automático de stack
        $this->emit('stp x29, x30, [sp, #-16]!');

        // Establecer nuevo frame pointer
        $this->emit('mov x29, sp');

        // Generar prólogo de callee-saved si es necesario
        $prologue = $this->func->generateCalleeSavedProlog();
        if (!empty($prologue)) {
            $this->emit($prologue);
        }

        // Reservar espacio en stack para locals y arrays
        $frameSize = $this->func->getFrameSize();
        if ($frameSize > 0) {
            // AArch64 requiere alineación a 16 bytes para sub
            $this->emit("sub sp, sp, #{$frameSize}");
        }

        // Guardar parámetros (que llegan en x0, x1, ..., x7)
        $this->phaseGenerateSaveParameters();

        $this->comment("frame.size=" . $frameSize . ", locals=" . count($this->func->getLocals()));
    }

    /**
     * Genera instrucciones para guardar parámetros del registro al stack.
     * Los parámetros enteros llegan en x0-x7.
     */
    protected function phaseGenerateSaveParameters(): void
    {
        if (!isset($this->func)) return;

        $locals = $this->func->getLocals();
        $paramRegister = 0;  // x0 para primer parámetro, x1 para segundo, etc.

        // Iterar sobre locales en orden de offset (primeros son parámetros)
        $sortedLocals = [];
        foreach ($locals as $name => $info) {
            $sortedLocals[$info['offset']] = ['name' => $name, 'type' => $info['type']];
        }
        ksort($sortedLocals);

        foreach ($sortedLocals as $offset => $varInfo) {
            if ($paramRegister >= 8) break;  // Solo x0-x7 para parámetros enteros

            $name = $varInfo['name'];
            $type = $varInfo['type'];

            // Validar que el tipo es entero (int32, int8, int16, etc.)
            if (in_array($type, ['int32', 'int8', 'int16', 'int64', 'rune'])) {
                // Generar instrucción para guardar parámetro
                $reg = 'x' . $paramRegister;
                $negOffset = -$offset;
                $this->emit("str {$reg}, [x29, #{$negOffset}]", "param {$name}");
                $paramRegister++;
            } elseif (in_array($type, ['float32', 'float64'])) {
                // Parámetros float llegan en s0-s7 (float32) o d0-d7 (float64)
                $sreg = ($type === 'float32') ? 's' . $paramRegister : 'd' . $paramRegister;
                $negOffset = -$offset;
                $this->emit("str {$sreg}, [x29, #{$negOffset}]", "param float {$name}");
                $paramRegister++;
            }
        }
    }

    /**
     * Genera epílogo de función: restaurar registros y retornar (versión fase).
     *
     * Secuencia:
     *   [callee-saved epilogue]      // si necesita restaurar callee-saved
     *   add sp, sp, #FRAME_SIZE      // liberar frame
     *   ldp x29, x30, [sp], #16      // restaurar FP y LR, incrementar sp
     *   ret                          // retornar a dirección en LR
     */
    protected function phaseGenerateEpilogue(): void
    {
        if (!isset($this->func)) return;

        // BARE-METAL: Si es _start, usar exit syscall
        if ($this->func->name === '_start') {
            $this->emit('mov x0, #0',        'exit code = 0');
            $this->emit('mov x8, #93',       'syscall exit');
            $this->emit('svc #0',            'invoke');
            return;
        }

        // Restaurar callee-saved si fue guardado
        $epilogue = $this->func->generateCalleeSavedEpilog();
        if (!empty($epilogue)) {
            $this->emit($epilogue);
        }

        // Liberar stack frame
        $frameSize = $this->func->getFrameSize();
        if ($frameSize > 0) {
            $this->emit("add sp, sp, #{$frameSize}");
        }

        // Restaurar x29 y x30, incrementar sp automáticamente
        $this->emit('ldp x29, x30, [sp], #16');

        // Retornar
        $this->emit('ret');
    }

    /**
     * Genera prólogo + código + epílogo para una función completa (versión fase).
     *
     * @param string $label nombre de función
     * @param object $blockCtx contexto del bloque (body)
     * @param FunctionContext $funcCtx contexto de la función
     */
    protected function phaseGenerateFunction(string $label, $funcCtx, $functionContext): void
    {
        // Cambiar contexto de función
        $oldFunc = $this->func;
        $this->func = $functionContext;

        // Prescan de parámetros (si la función los tiene)
        $this->phasePrescanFunctionParams($funcCtx);

        // Extraer bloque
        $blockCtx = null;
        if (method_exists($funcCtx, 'block')) {
            try {
                $blockCtx = $funcCtx->block();
            } catch (\Throwable $e) {}
        }

        // Prescan: registrar variables del bloque
        if ($blockCtx !== null) {
            $this->phasePrescanBlock($blockCtx);
        }

        // Generar prólogo
        $this->phaseGeneratePrologue($label);

        // Generar código del body (delega a ExpressionsHandler, ControlFlowHandler, etc.)
        if ($blockCtx !== null) {
            $this->phaseGenerateBlock($blockCtx);
        }

        // Generar epílogo
        $this->phaseGenerateEpilogue();

        // Restaurar contexto anterior
        $this->func = $oldFunc;
    }

    /**
     * Prescan de parámetros de función.
     * Registra cada parámetro como variable local en el stack.
     */
    protected function phasePrescanFunctionParams($funcCtx): void
    {
        if (!isset($this->func)) return;

        // Intentar obtener parámetros
        $params = null;
        if (method_exists($funcCtx, 'parameterList')) {
            try {
                $params = $funcCtx->parameterList();
            } catch (\Throwable $e) {}
        }

        if ($params === null) {
            return;
        }

        // Registrar cada parámetro
        for ($i = 0; $i < $params->getChildCount(); $i++) {
            $child = $params->getChild($i);

            // Saltar comas y otros tokens
            if ($child instanceof \TerminalNode) {
                continue;
            }

            $fullClass = get_class($child);
            $class = substr($fullClass, strrpos($fullClass, '\\') + 1);

            if ($class === 'NormalParameterContext') {
                // NormalParameter: ID type
                $paramName = null;
                
                // Intento 1: ID() (token)
                if (is_callable([$child, 'ID'])) {
                    try {
                        $id = $child->ID();
                        if ($id !== null) {
                            $paramName = $id->getText();
                        }
                    } catch (\Throwable $e) {}
                }

                if ($paramName !== null) {
                    // Extraer tipo
                    $paramType = 'int32';
                    if (method_exists($child, 'type')) {
                        try {
                            $typeCtx = $child->type();
                            if ($typeCtx !== null) {
                                $paramType = $this->getTypeName($typeCtx);
                            }
                        } catch (\Throwable $e) {}
                    }

                    // Registrar como variable local
                    $this->func->allocLocal($paramName, $paramType);
                }
            }
        }
    }

    /**
     * Genera una tabla de símbolos para depuración (versión fase).
     * Llamada al final de generateProgram para registrar todas las variables.
     */
    protected function phaseGenerateSymbolTable(): void
    {
        // Este método es llamado después de compilar todas las funciones
        // Necesita generar símbolos usando la información almacenada en
        // phaseCompileProgram (acceso a objetos FunctionContext guardados)

        // Si tenemos acceso a userFunctions (desde ProgramPhase), usarla
        if (isset($this->userFunctionContexts) && is_array($this->userFunctionContexts)) {
            foreach ($this->userFunctionContexts as $funcName => $funcCtx) {
                $this->addFunctionSymbols($funcCtx, $funcName);
            }
        }

        // Si no, intentar usar $this->func si existe
        if (!isset($this->userFunctionContexts) && isset($this->func)) {
            $locals = $this->func->getLocals();
            $arrays = $this->func->getArrays();

            // Registrar variables locales
            foreach ($locals as $name => $info) {
                $this->symbolTable[$name] = [
                    'type'   => $info['type'] ?? 'int32',
                    'offset' => $info['offset'] ?? 0,
                    'scope'  => 'local',
                ];
            }

            // Registrar arrays
            foreach ($arrays ?? [] as $name => $info) {
                $this->symbolTable[$name] = [
                    'type'   => $info['elem_type'] ?? 'int32',
                    'offset' => $info['base_offset'] ?? 0,
                    'scope'  => 'array',
                    'dims'   => $info['dims'] ?? [],
                ];
            }
        }
    }

    /**
     * Agrega símbolos de una función a la tabla de símbolos.
     */
    protected function addFunctionSymbols($funcCtx, string $funcName): void
    {
        // Agregar función misma
        $this->symbolTable[$funcName] = [
            'type'   => 'function',
            'scope'  => 'global',
        ];

        // Agregar variables locales de la función
        $locals = $funcCtx->getLocals();
        foreach ($locals as $name => $info) {
            $this->symbolTable[$name] = [
                'type'   => $info['type'] ?? 'int32',
                'offset' => $info['offset'] ?? 0,
                'scope'  => 'local',
                'function' => $funcName,
            ];
        }

        // Agregar arrays de la función
        $arrays = $funcCtx->getArrays();
        foreach ($arrays ?? [] as $name => $info) {
            $this->symbolTable[$name] = [
                'type'   => $info['elem_type'] ?? 'int32',
                'offset' => $info['base_offset'] ?? 0,
                'scope'  => 'array',
                'dims'   => $info['dims'] ?? [],
                'function' => $funcName,
            ];
        }
    }
}

