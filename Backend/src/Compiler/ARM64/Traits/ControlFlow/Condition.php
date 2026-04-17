<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

/**
 * IfTrait — Generación ARM64 para sentencias condicionales
 *
 * Soporta la sintaxis completa de Golampi:
 *   if cond { ... }
 *   if cond { ... } else { ... }
 *   if cond { ... } else if cond2 { ... } else { ... }
 *
 * Estrategia de generación (cadena de if-else):
 *
 *   Dado: if C1 { B1 } else if C2 { B2 } else { B3 }
 *
 *   [eval C1]
 *   cbz x0, .else_1          // falso → siguiente rama
 *   [B1 code]
 *   b .if_end
 *   .else_1:
 *     [eval C2]
 *     cbz x0, .else_2
 *     [B2 code]
 *     b .if_end
 *   .else_2:
 *     [B3 code]
 *   .if_end:
 *
 * Principio de compiladores (Aho et al. — backpatching):
 *   Usamos "forward labels" (.else_N, .if_end) que se resuelven durante
 *   la generación lineal del código (no necesitamos backpatching real
 *   porque PHP genera el assembly como strings ya con los labels completos).
 *
 * La condición DEBE ser de tipo bool (validado en análisis semántico).
 * No se permiten paréntesis en la condición (sintaxis Golampi/Go).
 */
trait Condition
{
    public function visitIfElseIfElse($ctx)
    {
        $conditions = $ctx->expression();  // array de condiciones
        $blocks     = $ctx->block();       // array de bloques
        $endLabel   = $this->newLabel('if_end');
        $n          = count($conditions);

        for ($i = 0; $i < $n; $i++) {
            $isLastCond = ($i === $n - 1);
            $hasElse    = (count($blocks) > $n);

            // Si hay más condiciones o un bloque else, necesitamos un label de salto
            $nextLabel = ($isLastCond && !$hasElse)
                ? $endLabel
                : $this->newLabel('else_branch');

            // ── Evaluar condición ─────────────────────────────────────────
            $this->comment('if condición #' . ($i + 1));
            $this->visit($conditions[$i]);

            // cbz: "compare and branch if zero" — salta si la condición es falsa
            $this->emit("cbz x0, $nextLabel", 'falso → siguiente rama');

            // ── Bloque verdadero ──────────────────────────────────────────
            $this->generateBlock($blocks[$i]);
            $this->emit("b $endLabel", 'saltar al final del if');

            // Emitir label de la siguiente rama (si no es el label final)
            if ($nextLabel !== $endLabel) {
                $this->label($nextLabel);
            }
        }

        // ── Bloque else (opcional) ────────────────────────────────────────
        if (count($blocks) > $n) {
            $this->comment('else');
            $this->generateBlock($blocks[$n]);
        }

        $this->label($endLabel);
        return null;
    }
}