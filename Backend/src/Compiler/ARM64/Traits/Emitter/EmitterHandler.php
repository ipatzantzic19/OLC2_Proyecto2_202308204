<?php

namespace Golampi\Compiler\ARM64\Traits\Emitter;

use Golampi\Compiler\ARM64\Traits\Emitter\InstructionEmitter;
use Golampi\Compiler\ARM64\Traits\Emitter\AssemblyBuilder;

require_once __DIR__ . '/InstructionEmitter.php';
require_once __DIR__ . '/AssemblyBuilder.php';

/**
 * EmitterTrait — Orquestador de la capa de emisión de código ARM64
 *
 * Coordina los dos sub-traits que forman la infraestructura de salida
 * del compilador. Es el nivel más bajo del generador: no conoce la
 * semántica del lenguaje, solo sabe cómo escribir instrucciones ARM64.
 *
 *   InstructionEmitter  → escritura de instrucciones individuales
 *                          emit(), label(), comment(), newLabel()
 *                          pushStack(), emitBinaryOp()
 *
 *   AssemblyBuilder     → construcción del archivo .s completo
 *                          buildAssembly() → ensambla .data + .text + helpers
 *
 * Separación de responsabilidades:
 *   - InstructionEmitter conoce el formato de cada línea ARM64.
 *   - AssemblyBuilder conoce la estructura global del archivo .s.
 *   - Los traits de nivel superior (ControlFlow, Expressions, etc.)
 *     solo llaman a emit() y label() sin preocuparse del formato.
 *
 * Estado que gestiona (definido en ARM64Generator):
 *   array $dataLines      → buffer de la sección .data
 *   array $textLines      → buffer de la sección .text
 *   array $postTextLines  → buffer de helpers de runtime
 *   int   $labelIdx       → contador global de etiquetas únicas
 */
trait EmmiterHandler
{
    use InstructionEmitter; // emit, label, comment, newLabel, pushStack, emitBinaryOp
    use AssemblyBuilder;    // buildAssembly
}