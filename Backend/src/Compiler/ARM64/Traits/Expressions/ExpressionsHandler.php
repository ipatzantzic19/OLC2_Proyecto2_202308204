<?php

namespace Golampi\Compiler\ARM64\Traits\Expressions;

use Golampi\Compiler\ARM64\Traits\Expressions\ExpressionEntry;
use Golampi\Compiler\ARM64\Traits\Expressions\LogicalOps;
use Golampi\Compiler\ARM64\Traits\Expressions\Comparisons;
use Golampi\Compiler\ARM64\Traits\Expressions\ArithmeticOps;
use Golampi\Compiler\ARM64\Traits\Expressions\UnaryOps;
use Golampi\Compiler\ARM64\Traits\FloatOps\FloatArithmetic;

require_once __DIR__ . '/ExpressionEntry.php';
require_once __DIR__ . '/LogicalOps.php';
require_once __DIR__ . '/Comparisons.php';
require_once __DIR__ . '/ArithmeticOps.php';
require_once __DIR__ . '/UnaryOps.php';
require_once __DIR__ . '/../FloatOps/FloatArithmetic.php';

/**
 * ExpressionsTrait — Orquestador de la fase de generación de expresiones
 *
 * Ensambla la jerarquía completa de evaluación de expresiones ARM64.
 * Cada sub-trait implementa un nivel específico de la precedencia de
 * operadores del lenguaje Golampi (enunciado sección 3.3.6–3.3.8).
 *
 * Jerarquía de precedencia (de menor a mayor prioridad):
 *
 *   ExpressionEntryTrait   → visitExpression (punto de entrada)
 *   LogicalOpsTrait        → || y && con evaluación de cortocircuito
 *   ComparisonsTrait       → ==, !=, >, >=, <, <=
 *   ArithmeticOpsTrait     → +, -, *, /, % con promoción de tipos int↔float
 *   UnaryOpsTrait          → -, !, &, * y passthrough de agrupación/arrays
 *
 * Nota: LiteralsTrait (literales: int32, float32, rune, string, bool, nil)
 * se mantiene como trait independiente en el nivel de Traits/ porque
 * es usado también por otros componentes del compilador.
 *
 * Convenciones de resultado (Aho et al. — "descriptores de registro"):
 *   - Resultado int32/bool/rune/string/pointer → registro x0
 *   - Resultado float32                        → registro s0
 *   - Cada visitor devuelve el tipo PHP string del resultado
 *
 * La pila de temporales ($func->pushTemp/$func->popTemp) se usa para
 * rastrear el uso máximo de slots temporales en el prescan de frame.
 *
 * Estado compartido (definido en ARM64Generator):
 *   ?FunctionContext $func  → función actual (para pushTemp/popTemp)
 *   array $textLines        → buffer de instrucciones ARM64
 *   int   $labelIdx         → contador de etiquetas únicas
 */
trait ExpressionsHandler
{
    // ── Punto de entrada ──────────────────────────────────────────────────
    use ExpressionEntry;   // visitExpression → delega a logicalOr

    // ── Operadores lógicos (menor precedencia) ────────────────────────────
    use LogicalOps;        // ||, && con cortocircuito

    // ── Operadores de comparación ─────────────────────────────────────────
    use Comparisons;       // ==, !=, >, >=, <, <=

    // ── Operadores aritméticos ────────────────────────────────────────────
    use ArithmeticOps;     // +, -, *, /, % con tabla de promoción
    use FloatArithmetic;   // pushFloatStack, popFloatStack, emitFloatBinaryOp

    // ── Operadores unarios (mayor precedencia) ────────────────────────────
    use UnaryOps;          // -, !, &ID, *ptr, (expr), array access
}