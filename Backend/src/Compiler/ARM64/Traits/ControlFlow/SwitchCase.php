<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

/**
 * SwitchTrait — Generación ARM64 para sentencias switch
 *
 * Soporta la sintaxis:
 *   switch expr {
 *     case v1, v2:  stmts
 *     case v3:      stmts
 *     default:      stmts
 *   }
 *
 * Estrategia de compilación (tabla de saltos lineal):
 *
 *   [eval switch expr]      // resultado en x0
 *   mov x19, x0             // guardar en callee-saved (no lo pisa el eval de cases)
 *
 *   [eval case v1] → cmp x19, x0 → b.eq .sw_case_0
 *   [eval case v2] → cmp x19, x0 → b.eq .sw_case_0
 *   [eval case v3] → cmp x19, x0 → b.eq .sw_case_1
 *   b .sw_default
 *
 *   .sw_case_0:  [body case 0] → b .sw_end
 *   .sw_case_1:  [body case 1] → b .sw_end
 *   .sw_default: [body default]
 *   .sw_end:
 *
 * Notas de compiladores (Aho et al.):
 *   - Golampi NO tiene fallthrough (a diferencia de C). Cada case termina con
 *     un salto implícito al final del switch.
 *   - x19 es callee-saved en AArch64 → seguro de usar durante la tabla de saltos.
 *   - Para switches con muchos casos, una tabla de saltos real (jump table)
 *     sería más eficiente, pero la búsqueda lineal es correcta y más simple
 *     para el alcance de este proyecto.
 *   - Los cases pueden tener múltiples valores separados por coma
 *     (case 1, 2, 3: ...) → se generan múltiples comparaciones que apuntan
 *     al mismo label de cuerpo.
 */
trait SwitchCase
{
    public function visitSwitchStatement($ctx)
    {
        $endLabel = $this->newLabel('sw_end');

        // ── Evaluar expresión del switch ──────────────────────────────────
        $this->comment('switch — evaluar expresión de control');
        $this->visit($ctx->expression());
        // x19 es callee-saved: no se corrompe durante la evaluación de cases
        $this->emit('mov x19, x0', 'valor del switch → x19 (callee-saved)');

        $cases    = $ctx->caseClause();
        $default_ = $ctx->defaultClause();

        // Label del default (si no hay default, salta al final)
        $defaultLabel = $endLabel;
        if ($default_ !== null) {
            $defaultLabel = $this->newLabel('sw_default');
        }

        // ── Pre-asignar labels para cada case ─────────────────────────────
        $caseLabels = [];
        foreach ($cases as $k => $_) {
            $caseLabels[$k] = $this->newLabel('sw_case');
        }

        // ── Tabla de comparaciones (dispatch table lineal) ─────────────────
        $this->comment('switch — tabla de comparaciones');
        foreach ($cases as $k => $case) {
            $exprList = $case->expressionList();
            // Iterar sobre los valores del case (separados por coma)
            for ($i = 0; $i < $exprList->getChildCount(); $i += 2) {
                $this->visit($exprList->getChild($i));  // valor → x0
                $this->emit('cmp x19, x0', "comparar switch vs case[$k]");
                $this->emit("b.eq {$caseLabels[$k]}", 'coincide → saltar al cuerpo');
            }
        }
        // Ningún case coincidió → ir al default (o al final)
        $this->emit("b $defaultLabel", 'ningún case → default/end');

        // ── Cuerpos de cada case ───────────────────────────────────────────
        foreach ($cases as $k => $case) {
            $this->label($caseLabels[$k]);
            $this->comment("case[$k] — cuerpo");

            // Visitar todos los nodos hijos que sean declaraciones/sentencias
            for ($i = 0; $i < $case->getChildCount(); $i++) {
                $child = $case->getChild($i);
                if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                    $this->visit($child);
                }
            }
            // Sin fallthrough: saltar siempre al final del switch
            $this->emit("b $endLabel", 'no fallthrough — saltar al final');
        }

        // ── Cuerpo del default (opcional) ─────────────────────────────────
        if ($default_ !== null) {
            $this->label($defaultLabel);
            $this->comment('switch — default');
            for ($i = 0; $i < $default_->getChildCount(); $i++) {
                $child = $default_->getChild($i);
                if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                    $this->visit($child);
                }
            }
        }

        $this->label($endLabel);
        return null;
    }
}