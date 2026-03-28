<?php

namespace Golampi\Compiler\ARM64\Traits\Literals;

use Golampi\Compiler\ARM64\Traits\Literals\IntLiteral;
use Golampi\Compiler\ARM64\Traits\Literals\FloatLiteral;
use Golampi\Compiler\ARM64\Traits\Literals\RuneLiteral;
use Golampi\Compiler\ARM64\Traits\Literals\StringLiteral;
use Golampi\Compiler\ARM64\Traits\Literals\ScalarLiteral;

require_once __DIR__ . '/IntLiteral.php';
require_once __DIR__ . '/FloatLiteral.php';
require_once __DIR__ . '/RuneLiteral.php';
require_once __DIR__ . '/StringLiteral.php';
require_once __DIR__ . '/ScalarLiteral.php';

/**
 * LiteralsTrait — Orquestador de generación de literales ARM64
 *
 * Agrupa los visitors de todos los tipos de literales del lenguaje Golampi.
 * Cada sub-trait es responsable de generar el código ARM64 correcto para
 * su tipo y dejar el resultado en el registro estándar:
 *   - x0  → int32, rune, bool, nil, string (puntero)
 *   - s0  → float32
 *
 * Sub-traits y tipos que cubren:
 *
 *   IntLiteral    → visitIntLiteral    : 0–2^31, negativos, con movk para >16 bits
 *   FloatLiteral  → visitFloatLiteral  : constante en .data, cargada con adrp+ldr s0
 *   RuneLiteral   → visitRuneLiteral   : codepoint Unicode en x0, secuencias de escape
 *   StringLiteral → visitStringLiteral : puntero a C-string en .data, adrp+add x0
 *   ScalarLiteral → visitTrueLiteral   : mov x0, #1
 *                   visitFalseLiteral  : mov x0, xzr
 *                   visitNilLiteral    : mov x0, xzr
 *                   visitInnerArrayLiteral : passthrough (Fase 3)
 *
 * Dependencias de otros traits:
 *   FloatLiteral  usa FloatPool::internFloat    (FloatOpsTrait)
 *   StringLiteral usa StringPool::internString  (StringPoolTrait)
 *
 * Todos los literales devuelven el nombre PHP del tipo como string,
 * lo que permite a los traits de expresiones determinar el tipo del
 * resultado y aplicar la promoción correcta (tabla sección 3.3.6).
 */
trait LiteralsHandler
{
    use IntLiteral;    // visitIntLiteral    → int32, mov/movk en x0
    use FloatLiteral;  // visitFloatLiteral  → float32, adrp+ldr en s0
    use RuneLiteral;   // visitRuneLiteral   → rune (alias int32), mov en x0
    use StringLiteral; // visitStringLiteral → string, adrp+add en x0
    use ScalarLiteral; // visitTrueLiteral, visitFalseLiteral, visitNilLiteral
}