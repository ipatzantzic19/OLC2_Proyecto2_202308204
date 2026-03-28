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
 * HelpersTrait — Orquestador de utilidades compartidas del compilador
 *
 * Agrupa los cuatro sub-traits de infraestructura que son usados
 * transversalmente por todos los demás traits del generador ARM64.
 *
 * Sub-traits y responsabilidades:
 *
 *   TypeResolver      → getTypeName($typeCtx) → 'int32' | 'float32' | 'bool' | ...
 *                        Normaliza los nodos de tipo del AST ANTLR4 a strings
 *                        internos del compilador.
 *
 *   FrameAllocator    → allocVar(name, type, line, col) → offset | null
 *                        storeDefault(type, offset) → código ARM64 del valor por defecto
 *                        Gestiona el stack frame mediante FunctionContext.
 *
 *   SymbolManager     → addSymbol / getSymbolTable / addError / getErrors
 *                        Tabla de símbolos y tabla de errores del compilador.
 *                        Son los reportes obligatorios del enunciado (sección 3.5).
 *
 *   IdentifierVisitor → visitIdentifier($ctx)
 *                        Genera ldr x0/s0 para leer una variable del frame.
 *                        Aplica descriptor de dirección (Aho et al.) para
 *                        determinar el registro destino según el tipo.
 *
 * Posición en la arquitectura:
 *   Estos helpers son los cimientos sobre los que construyen todos los traits
 *   de nivel superior (Declarations, Expressions, ControlFlow, etc.).
 *   No dependen de ningún otro trait del compilador.
 */
trait HelpersHandler
{
    use TypeResolver;      // getTypeName → normalización de tipos del AST
    use FrameAllocator;    // allocVar, storeDefault → gestión del stack frame
    use SymbolManager;     // addSymbol, addError → reportes del compilador
    use IdentifierVisitor; // visitIdentifier → ldr x0/s0 desde el frame
}