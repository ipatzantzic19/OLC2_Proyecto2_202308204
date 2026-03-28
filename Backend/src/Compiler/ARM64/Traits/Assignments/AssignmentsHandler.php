<?php

namespace Golampi\Compiler\ARM64\Traits\Assignments;

use Golampi\Compiler\ARM64\Traits\Assignments\SimpleAssignment;
use Golampi\Compiler\ARM64\Traits\Assignments\ArrayAssignment;
use Golampi\Compiler\ARM64\Traits\Assignments\PointerAssignment;
use Golampi\Compiler\ARM64\Traits\Assignments\IncrementDecrement;

require_once __DIR__ . '/SimpleAssignment.php';
require_once __DIR__ . '/ArrayAssignment.php';
require_once __DIR__ . '/PointerAssignment.php';
require_once __DIR__ . '/IncrementDecrement.php';

/**
 * AssignmentsHandler — Orquestador de la fase de asignaciones ARM64
 *
 * Agrupa todos los sub-traits que generan código para las distintas
 * formas de asignación del lenguaje Golampi (enunciado sección 3.3.5):
 *
 *   SimpleAssignment    → x = expr  /  x OP= expr
 *                         Soporta todos los operadores compuestos: +=, -=, *=, /=
 *                         con coerción automática int32 ↔ float32.
 *
 *   ArrayAssignment     → a[i] = expr  /  m[i][j] = expr
 *                         Stub en Fase 2; lógica completa en Fase 3.
 *
 *   PointerAssignment   → *ptr = expr  /  *ptr OP= expr
 *                         Escribe a través de la dirección almacenada en ptr.
 *
 *   IncrementDecrement  → x++  /  x--
 *                         Sentencias (no expresiones). Solo int32 y rune.
 *
 * Modelo de compiladores (Aho et al. — lvalues y rvalues):
 *   - Un lvalue es una ubicación de memoria (slot en frame o dirección de puntero).
 *   - Un rvalue es el valor de una expresión.
 *   - La asignación evalúa el rvalue y lo almacena en la ubicación del lvalue.
 *   - FunctionContext actúa como la "tabla de descriptores de dirección"
 *     que mapea nombre → offset en stack frame.
 *
 * Estado compartido (definido en ARM64Generator):
 *   ?FunctionContext $func  → función actual (offset resolver)
 *   array $textLines        → buffer de instrucciones ARM64
 */
trait AssignmentsHandler
{
    use SimpleAssignment;    // x = expr  /  x OP= expr (int32 y float32)
    use ArrayAssignment;     // a[i] = expr (stub Fase 2, completo Fase 3)
    use PointerAssignment;   // *ptr = expr  /  *ptr OP= expr
    use IncrementDecrement;  // x++  /  x--
}