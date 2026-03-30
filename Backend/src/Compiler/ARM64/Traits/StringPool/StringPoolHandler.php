<?php

namespace Golampi\Compiler\ARM64\Traits\StringPool;

use Golampi\Compiler\ARM64\Traits\StringPool\PoolTrait;

require_once __DIR__ . '/PoolTrait.php';

/**
 * StringPoolHandler — Orquestador del pool de strings
 *
 * Coordina el sub-trait PoolTrait que implementa la deduplicación
 * e internado de cadenas constantes.
 *
 * Responsabilidades:
 *   - internString(s)  → obtener o crear label para cadena s en .data
 *   - asmEscape(s)     → escapar sintaxis en cadena para assembly
 *
 * Separación de responsabilidades:
 *   - PoolTrait  → logística de deduplicación y escaping
 *   - Este handler → orquestación (punto de entrada del nivel superior)
 *
 * Estado que gestiona (definido en ARM64Generator):
 *   array $stringPool  → hash raw_value => label
 *   int   $strIdx      → contador global de string labels
 *   array $dataLines   → buffer de sección .data
 */
trait StringPoolHandler
{
    use PoolTrait;  // internString, asmEscape
}
