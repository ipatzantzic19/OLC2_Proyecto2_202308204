<?php

namespace Golampi\Compiler\ARM64\Traits\Helpers;

use Golampi\Compiler\ARM64\Traits\Helpers\TypeResolver;
use Golampi\Compiler\ARM64\Traits\Helpers\FrameAllocator;
use Golampi\Compiler\ARM64\Traits\Helpers\SymbolManager;
use Golampi\Compiler\ARM64\Traits\Helpers\IdentifierVisitor;

require_once __DIR__ . '/TypeResolver.php';
require_once __DIR__ . '/FrameAllocator.php';
require_once __DIR__ . '/SymbolManager.php';
require_once __DIR__ . '/IdentifierVisitor.php';

/**
 * HelpersHandler — Orquestador de utilidades compartidas del compilador
 *
 * Agrupa los cuatro sub-traits de infraestructura que son usados
 * transversalmente por todos los demás traits del generador ARM64.
 *
 * Siguiendo el modelo de compiladores (Aho et al.), estos helpers corresponden
 * a los "servicios transversales" que soportan las fases de generación:
 *
 *   TypeResolver      → Sistema de tipos: getTypeName($typeCtx)
 *                        Traduce nodos AST ANTLR4 a identificadores de tipo
 *                        internos ('int32', 'float32', 'bool', etc.).
 *                        Análogo al "type record system" del libro.
 *
 *   FrameAllocator    → Gestión del registro de activación (stack frame):
 *                        allocVar(name, type, line, col) → offset | null
 *                        storeDefault(type, offset) → código ARM64 del valor por defecto
 *                        Implementa la tabla de descriptores de dirección (Aho et al.)
 *                        que mapea cada variable a su slot en el stack frame.
 *
 *   SymbolManager     → Tabla de símbolos y tabla de errores del compilador.
 *                        addSymbol / getSymbolTable → reporte de tabla de símbolos
 *                        addError / getErrors       → reporte de errores
 *                        Reportes obligatorios según enunciado sección 3.5.
 *
 *   IdentifierVisitor → Lectura de variables: visitIdentifier($ctx)
 *                        Genera ldr x0/s0 desde el slot en el stack frame.
 *                        Aplica el concepto de "descriptor de registro" (Aho et al.):
 *                        determina el registro destino según el tipo de la variable.
 *
 * Posición en la arquitectura:
 *   Este handler es la base sobre la que construyen todos los demás traits.
 *   No depende de ningún otro trait del compilador. Cualquier trait que
 *   necesite acceder a la tabla de símbolos, gestionar el frame o resolver
 *   tipos debe hacerlo a través de los métodos de este handler.
 *
 * Estado que gestiona (definido en ARM64Generator):
 *   ?FunctionContext $func  → función actual con tabla de slots
 *   array $symbolTable      → tabla de símbolos acumulada
 *   array $errors           → errores semánticos acumulados
 */
trait HelpersHandler
{
    use TypeResolver;      // getTypeName → normalización de tipos del AST ANTLR4
    use FrameAllocator;    // allocVar, storeDefault → gestión del registro de activación
    use SymbolManager;     // addSymbol, addError → reportes formales del compilador
    use IdentifierVisitor; // visitIdentifier → ldr x0/s0 desde el stack frame
}