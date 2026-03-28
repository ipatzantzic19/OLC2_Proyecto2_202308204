<?php

namespace Golampi\Compiler\ARM64\Traits\FloatOps;

use Golampi\Compiler\ARM64\Traits\FloatOps\FloatPool;
use Golampi\Compiler\ARM64\Traits\FloatOps\FloatArithmetic;
use Golampi\Compiler\ARM64\Traits\FloatOps\FloatComparison;

require_once __DIR__ . '/FloatPool.php';
require_once __DIR__ . '/FloatArithmetic.php';
require_once __DIR__ . '/FloatComparison.php';

/**
 * FloatHandler — Orquestador de operaciones float32 ARM64
 *
 * Agrupa todas las capacidades necesarias para compilar expresiones
 * y variables de tipo float32 a código ARM64 (AArch64) correcto.
 *
 * El tipo float32 en Golampi corresponde a IEEE-754 de precisión simple
 * (32 bits), almacenado en registros SIMD s0–s31 de AArch64.
 *
 * Sub-traits y responsabilidades:
 *
 *   FloatPool         → gestión de constantes float32 en sección .data
 *                        internFloat(val) → label; deduplicación por bits IEEE-754
 *
 *   FloatArithmetic   → operaciones aritméticas sobre registros SIMD
 *                        pushFloatStack / popFloatStack (gestión de temporales)
 *                        emitFloatBinaryOp(op) → fadd/fsub/fmul/fdiv
 *
 *   FloatComparison   → comparaciones y conversiones de tipo
 *                        emitFloatComparison(op) → fcmp + cset
 *                        emitIntToFloat()         → scvtf s0, w0
 *                        emitFloatToInt()         → fcvtzs w0, s0
 *                        emitFloat32ToDouble()    → fcvt d0, s0 (para printf)
 *
 * Relación con otros traits:
 *   - LiteralsTrait::visitFloatLiteral usa FloatPool::internFloat
 *   - ExpressionsTrait::ArithmeticOpsTrait usa FloatArithmetic y FloatComparison
 *   - FunctionCallTrait usa emitFloat32ToDouble para fmt.Println(float)
 *   - AssignmentsTrait usa pushFloatStack/popFloatStack para operadores compuestos
 *
 * Estado compartido (definido en ARM64Generator):
 *   array $floatPool  → cache de constantes float (bits → label)
 *   int   $floatIdx   → contador de constantes float en .data
 */
trait FloatHandler
{
    use FloatPool;       // internFloat, floatToAsmLiteral
    use FloatArithmetic; // pushFloatStack, popFloatStack, emitFloatBinaryOp
    use FloatComparison; // emitFloatComparison, emitIntToFloat, emitFloatToInt, emitFloat32ToDouble
}