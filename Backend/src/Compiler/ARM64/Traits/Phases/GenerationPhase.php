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

        // ✅ CORRECCIÓN: Todas las funciones (inc. _start) necesitan frame pointer si usan locales
        // Según PDF: x29 debe inicializarse SIEMPRE si se usa para acceso a variables
        // Guardar x29 (FP) y x30 (LR) con decremento automático de stack
        $this->emit('stp x29, x30, [sp, #-16]!', 'guardar frame pointer y link register');

        // Establecer nuevo frame pointer
        $this->emit('mov x29, sp', 'x29 = frame pointer (SP actual)');

        // Generar prólogo de callee-saved si es necesario
        $prologue = $this->func->generateCalleeSavedProlog();
        if (!empty($prologue)) {
            $this->emit($prologue);
        }

        // Reservar espacio en stack para locals y arrays
        $frameSize = $this->func->getFrameSize();
        $this->activeFrameSize = $frameSize;
        if ($frameSize > 0) {
            // AArch64 requiere alineación a 16 bytes para sub
            $this->emit("sub sp, sp, #{$frameSize}", "reservar {$frameSize} bytes para variables locales");
        }

        // Guardar parámetros (que llegan en x0, x1, ..., x7)
        $this->phaseGenerateSaveParameters();

        $this->comment("frame.size=" . $frameSize . ", locals=" . count($this->func->getLocals()));
    }

    /**
     * ✅ CORRECCIÓN: Genera instrucciones para guardar SOLO parámetros del registro al stack.
     * Los parámetros enteros llegan en x0-x7.
     * 
     * Las variables locales NO son parámetros y no deben guardarse aquí.
     * Se inicializan con sus valores por defecto al visitarlas.
     */
    protected function phaseGenerateSaveParameters(): void
    {
        if (!isset($this->func)) return;

        $locals = $this->func->getLocals();
        $paramRegister = 0;  // x0 para primer parámetro, x1 para segundo, etc.

        // Iterar sobre locales en orden de offset, pero SOLO procesar parámetros (is_param = true)
        $sortedLocals = [];
        foreach ($locals as $name => $info) {
            // ✅ Solo incluir parámetros reales
            if (!empty($info['is_param'])) {
                $sortedLocals[$info['offset']] = ['name' => $name, 'type' => $info['type']];
            }
        }
        ksort($sortedLocals);

        foreach ($sortedLocals as $offset => $varInfo) {
            if ($paramRegister >= 8) break;  // Solo x0-x7 para parámetros enteros

            $name = $varInfo['name'];
            $type = $varInfo['type'];

            // ✅ CORRECCIÓN: Usar registros adecuados según el tipo
            if (in_array($type, ['int32', 'rune'])) {
                // int32 y rune: parámetros llegan en xN pero almacenamos con wN (32-bit)
                $reg = 'w' . $paramRegister;
                $negOffset = -$offset;
                $this->emit("str $reg, [x29, #$negOffset]", "param $name ($type, 32-bit)");
                $paramRegister++;
            } elseif ($type === 'bool') {
                // bool llegaen xN pero almacenamos con wN (32-bit)
                $reg = 'w' . $paramRegister;
                $negOffset = -$offset;
                $this->emit("str $reg, [x29, #$negOffset]", "param $name (bool, 32-bit)");
                $paramRegister++;
            } elseif (in_array($type, ['int8', 'int16', 'int64'])) {
                // int8, int16, int64 → usar registros enteros
                $reg = 'x' . $paramRegister;
                $negOffset = -$offset;
                $this->emit("str $reg, [x29, #$negOffset]", "param $name ($type)");
                $paramRegister++;
            } elseif (in_array($type, ['float32', 'float64'])) {
                // Parámetros float llegan en s0-s7 (float32) o d0-d7 (float64)
                $sreg = ($type === 'float32') ? 's' . $paramRegister : 'd' . $paramRegister;
                $negOffset = -$offset;
                $this->emit("str $sreg, [x29, #$negOffset]", "param $name ($type)");
                $paramRegister++;
            } else {
                // Puntero, string → x-register (64-bit)
                $reg = 'x' . $paramRegister;
                $negOffset = -$offset;
                $this->emit("str $reg, [x29, #$negOffset]", "param $name ($type)");
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
            // ✅ EPÍLOGO COMPLETO: restaurar stack frame antes de exit (completitud académica)
            $frameSize = $this->activeFrameSize ?? $this->func->getFrameSize();
            if ($frameSize > 0) {
                $this->emit("add sp, sp, #{$frameSize}", 'restaurar stack pointer');
            }
            $this->emit('ldp x29, x30, [sp], #16', 'restaurar frame pointer y link register');

            // Si se usó printf, forzar flush antes de salir por syscall directo.
            $this->emit('mov x0, xzr',      'fflush(NULL)');
            $this->emit('bl fflush',        'vaciar buffers stdio');
            
            // Syscalls de salida
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
        $frameSize = $this->activeFrameSize ?? $this->func->getFrameSize();
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
        $this->activeFrameSize = null;

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
        $this->activeFrameSize = null;
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
     * 
     * ✅ NOTA: Ya no es necesario aquí. Los símbolos se registran correctamente
     * mediante addSymbol() en ShortVarDecl, VarDecl, ConstDecl, etc. durante la
     * generación. Este método fue una fallback que causaba duplicados con scope "local".
     * 
     * Se mantiene como placeholder para permitir futuras optimizaciones sin
     * quebrar llamadas externas.
     */
    protected function phaseGenerateSymbolTable(): void
    {
        // Ahora los símbolos se registran directamente durante visitadores,
        // no aquí. Esto evita duplicados y mantiene los scopes correctos.
    }

    /**
     * Agrega símbolos de una función a la tabla de símbolos.
     * 
     * ✅ NOTA: Este método también está deprecado por la misma razón.
     * Los símbolos se registran en tiempo real durante la generación.
     */
    protected function addFunctionSymbols($funcCtx, string $funcName): void
    {
        // Deprecado: símbolos se registran via addSymbol() durante la compilación
    }
}

