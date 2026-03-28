<?php

namespace Golampi\Compiler\ARM64\Traits\Expressions;

/**
 * LogicalOpsTrait — Generación ARM64 para operadores lógicos
 *
 * Implementa los operadores lógicos binarios de Golampi:
 *   &&  (AND lógico con cortocircuito)
 *   ||  (OR  lógico con cortocircuito)
 *
 * Evaluación de cortocircuito (short-circuit evaluation):
 *   El enunciado del proyecto especifica que AMBOS operadores DEBEN usar
 *   evaluación de cortocircuito. Esto es parte de la semántica del lenguaje
 *   (no solo una optimización).
 *
 *   a && b → si a == false, b no se evalúa (resultado false)
 *   a || b → si a == true,  b no se evalúa (resultado true)
 *
 * Generación ARM64 para && (AND):
 *
 *   [eval a]                  // resultado en x0
 *   cbz x0, .and_end          // si a es false → cortocircuito, skip b
 *   [eval b]                  // resultado en x0
 *   ... (más operandos si los hay)
 *   .and_end:
 *   cmp x0, #0
 *   cset x0, ne               // normalizar a 0 o 1
 *
 * Generación ARM64 para || (OR):
 *
 *   [eval a]                  // resultado en x0
 *   cbnz x0, .or_end          // si a es true → cortocircuito, skip b
 *   [eval b]                  // resultado en x0
 *   .or_end:
 *   cmp x0, #0
 *   cset x0, ne               // normalizar a 0 o 1
 *
 * Nota: cbnz = "compare and branch if non-zero" (saltar si true)
 *       cbz  = "compare and branch if zero"     (saltar si false)
 */
trait LogicalOps
{
    // ── OR lógico ─────────────────────────────────────────────────────────

    public function visitLogicalOr($ctx)
    {
        // Caso base: sin operador || → delegar al nivel inferior
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->logicalAnd(0));
        }

        $endLabel = $this->newLabel('or_end');

        // Evaluar primer operando
        $this->visit($ctx->logicalAnd(0));
        // Cortocircuito: si true, saltamos al final (resultado ya en x0)
        $this->emit("cbnz x0, $endLabel", 'cortocircuito OR: si true → saltar');

        // Evaluar operandos restantes
        $aIdx = 1;
        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $this->visit($ctx->logicalAnd($aIdx++));
            if ($i < $ctx->getChildCount() - 2) {
                $this->emit("cbnz x0, $endLabel", 'cortocircuito OR intermedio');
            }
        }

        $this->label($endLabel);
        // Normalizar resultado a 0 o 1
        $this->emit('cmp x0, #0');
        $this->emit('cset x0, ne', 'bool resultado OR');
        return 'bool';
    }

    // ── AND lógico ────────────────────────────────────────────────────────

    public function visitLogicalAnd($ctx)
    {
        // Caso base: sin operador && → delegar al nivel inferior
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->equality(0));
        }

        $endLabel = $this->newLabel('and_end');

        // Evaluar primer operando
        $this->visit($ctx->equality(0));
        // Cortocircuito: si false, saltamos al final (resultado 0 ya en x0)
        $this->emit("cbz x0, $endLabel", 'cortocircuito AND: si false → saltar');

        // Evaluar operandos restantes
        $eIdx = 1;
        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $this->visit($ctx->equality($eIdx++));
            if ($i < $ctx->getChildCount() - 2) {
                $this->emit("cbz x0, $endLabel", 'cortocircuito AND intermedio');
            }
        }

        $this->label($endLabel);
        $this->emit('cmp x0, #0');
        $this->emit('cset x0, ne', 'bool resultado AND');
        return 'bool';
    }
}