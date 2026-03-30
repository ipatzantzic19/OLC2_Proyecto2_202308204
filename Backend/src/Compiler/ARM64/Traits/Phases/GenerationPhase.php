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
        $this->emitLabel($label);

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

        $this->emitComment("frame.size=" . $frameSize . ", locals=" . count($this->func->getLocals()));
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
    protected function phaseGenerateFunction(string $label, $blockCtx, $funcCtx): void
    {
        // Cambiar contexto de función
        $oldFunc = $this->func;
        $this->func = $funcCtx;

        // Prescan: registrar variables
        $this->phasePrescanBlock($blockCtx);

        // Generar prólogo
        $this->phaseGeneratePrologue($label);

        // Generar código del body (delega a ExpressionsHandler, ControlFlowHandler, etc.)
        $this->phaseGenerateBlock($blockCtx);

        // Generar epílogo
        $this->phaseGenerateEpilogue();

        // Restaurar contexto anterior
        $this->func = $oldFunc;
    }

    /**
     * Genera una tabla de símbolos para depuración (versión fase).
     * Llamada al final de generateProgram para registrar todas las variables.
     */
    protected function phaseGenerateSymbolTable(): void
    {
        if (!isset($this->func)) return;

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
