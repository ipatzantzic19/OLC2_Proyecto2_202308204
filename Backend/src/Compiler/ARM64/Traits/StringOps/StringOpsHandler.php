<?php

namespace Golampi\Compiler\ARM64\Traits\StringOps;

use Golampi\Compiler\ARM64\Traits\StringOps\ConcatHelper;
use Golampi\Compiler\ARM64\Traits\StringOps\NowHelper;
use Golampi\Compiler\ARM64\Traits\StringOps\SubstrHelper;
use Golampi\Compiler\ARM64\Traits\StringOps\StringLenHelper;
use Golampi\Compiler\ARM64\Traits\StringOps\TypeOfHelper;

require_once __DIR__ . '/ConcatHelper.php';
require_once __DIR__ . '/NowHelper.php';
require_once __DIR__ . '/SubstrHelper.php';
require_once __DIR__ . '/StringLenHelper.php';
require_once __DIR__ . '/TypeOfHelper.php';

/**
 * StringOpsHandler — Orquestador de operaciones de string
 *
 * Coordina todos los sub-traits que implementan operaciones sobre
 * cadenas de caracteres: concatenación, longitud, substring,
 * fecha/hora actual, e inspección de tipos.
 *
 * Sobre strings en Golampi:
 *   - Son punteros a C-strings null-terminated (\0) en memoria
 *   - Literales viven en .data (read-only)
 *   - Resultados de operaciones viven en heap (malloc)
 *   - Se apoyan en funciones de la libc estándar
 *
 * Operaciones coordinadas:
 *   ConcatHelper    → concatenación (+) con golampi_concat helper
 *   StringLenHelper → len(s) usando strlen
 *   SubstrHelper    → substr(s, start, length) con golampi_substr helper
 *   NowHelper       → now() con golampi_now usando time/localtime/strftime
 *   TypeOfHelper    → typeOf(x) → nombre del tipo como string constante
 *
 * Separación de responsabilidades:
 *   - Cada helper conoce su operación específica y sus helpers ARM64
 *   - Este handler orquesta todos los helpers para acceso transparente
 *   - ARM64Generator usa este handler sin conocer detalles de cada operación
 *
 * Estado que gestiona (definido en ARM64Generator):
 *   array $postTextLines → buffer de helpers de runtime
 *   bool $concatHelperEmitted, $substrHelperEmitted, $nowHelperEmitted
 */
trait StringOpsHandler
{
    use ConcatHelper;      // emitStringConcat, ensureConcatHelper
    use StringLenHelper;   // emitStrlen
    use SubstrHelper;      // emitSubstr, ensureSubstrHelper
    use NowHelper;         // emitNow, ensureNowHelper
    use TypeOfHelper;      // emitTypeOf
}
