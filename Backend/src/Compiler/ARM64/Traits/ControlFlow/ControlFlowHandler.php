<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

use Golampi\Compiler\ARM64\Traits\ControlFlow\ForClassic;
use Golampi\Compiler\ARM64\Traits\ControlFlow\ForWhile;
use Golampi\Compiler\ARM64\Traits\ControlFlow\ForInfinite;
use Golampi\Compiler\ARM64\Traits\ControlFlow\Condition;
use Golampi\Compiler\ARM64\Traits\ControlFlow\SwitchCase;
use Golampi\Compiler\ARM64\Traits\ControlFlow\Transfer;

require_once __DIR__ . '/ControlFlow/ForClassic.php';
require_once __DIR__ . '/ControlFlow/ForWhile.php';
require_once __DIR__ . '/ControlFlow/ForInfinite.php';
require_once __DIR__ . '/ControlFlow/Condition.php';
require_once __DIR__ . '/ControlFlow/SwitchCase.php';
require_once __DIR__ . '/ControlFlow/Transfer.php';

/**
 * ControlFlowTrait — Orquestador del control de flujo ARM64
 *
 * Este trait actúa como fachada (Facade pattern) que reúne todos los
 * sub-traits especializados de control de flujo. Cada sub-trait implementa
 * una fase del control de flujo del compilador:
 *
 *   ForClassicTrait   → for init ; cond ; post { }
 *   ForWhileTrait     → for cond { }              (estilo while)
 *   ForInfiniteTrait  → for { }                   (bucle infinito)
 *   IfTrait           → if / else if / else
 *   SwitchCaseTrait   → switch / case / default
 *   TransferTrait     → break / continue / return  + passthrough
 *
 * Organización basada en el modelo de control de flujo de compiladores
 * (Aho, Lam, Sethi — "Compiladores: Principios, Técnicas y Herramientas"):
 *
 *   - Cada estructura de control genera un sub-grafo de flujo de control
 *     bien definido con entradas y salidas explícitas.
 *   - Los labels ARM64 corresponden a los nodos del CFG (Control Flow Graph).
 *   - La pila $loopStack mantiene el contexto de break/continue, análoga a
 *     la tabla de control de sentencias anidadas del libro.
 *
 * Estado compartido que usan todos los sub-traits (definido en ARM64Generator):
 *   array  $loopStack   → pila de { 'break' => label, 'continue' => label }
 *   ?FunctionContext $func → función actual (para epílogo en return)
 */
trait ControlFlowHandler
{
    // ── Bucles ────────────────────────────────────────────────────────────
    use ForClassic;    // for clásico con init/cond/post
    use ForWhile;      // for estilo while (solo condición)
    use ForInfinite;   // for infinito (sin condición)

    // ── Condicionales ─────────────────────────────────────────────────────
    use Condition;     // if / else if / else
    use SwitchCase;        // switch / case / default

    // ── Transferencia de control ──────────────────────────────────────────
    use Transfer;      // break / continue / return + block passthrough
}