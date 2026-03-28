<?php

namespace Golampi\Compiler\ARM64\Traits\Expressions;

/**
 * UnaryOpsTrait — Generación ARM64 para operadores unarios
 *
 * Implementa todos los operadores unarios del lenguaje Golampi:
 *
 *   -expr     → negación aritmética (int32 o float32)
 *   !expr     → negación lógica (bool)
 *   &ID       → dirección de memoria (address-of)
 *   *expr     → desreferencia de puntero
 *   (expr)    → agrupación (passthrough)
 *
 * Generación ARM64:
 *
 *   -x  (int32):   neg x0, x0
 *   -x  (float32): fneg s0, s0
 *   !x  (bool):    eor x0, x0, #1   (XOR con 1 invierte el bit menos significativo)
 *   &ID:           sub x0, x29, #offset  (dirección relativa al frame pointer)
 *   *ptr:          ldr x0, [x0]          (carga el valor apuntado)
 *
 * Nota sobre address-of (&ID):
 *   En ARM64, las variables locales viven en el stack relativo a x29 (fp).
 *   La dirección de la variable x con offset N es: x29 - N
 *   Instrucción: sub x0, x29, #N  (subtract immediate del fp)
 *
 * Nota sobre dereference (*ptr):
 *   Fase 2: asumimos que el puntero apunta a int32 (ldr x0, [x0]).
 *   Fase 3 ampliará esto para tipos float, arrays, etc.
 *
 * También incluye el passthrough de visitPrimaryUnary y visitGroupedExpression
 * por cohesión (ambos delegan sin transformación).
 */
trait UnaryOps
{
    // ── Passthrough de unario primario ────────────────────────────────────

    public function visitPrimaryUnary($ctx)
    {
        return $this->visit($ctx->primary());
    }

    // ── Negación aritmética: -expr ────────────────────────────────────────

    public function visitNegativeUnary($ctx)
    {
        $type = $this->visit($ctx->unary());

        if ($type === 'float32') {
            // Negación de registro SIMD: fneg s0, s0
            $this->emit('fneg s0, s0', 'negación float32');
        } else {
            // Negación entera: neg x0, x0  (equivale a rsb x0, x0, #0)
            $this->emit('neg x0, x0', 'negación int32');
        }

        return $type;
    }

    // ── Negación lógica: !expr ────────────────────────────────────────────

    public function visitNotUnary($ctx)
    {
        $this->visit($ctx->unary());
        // XOR del bit menos significativo: invierte true↔false
        // 0 XOR 1 = 1  (false → true)
        // 1 XOR 1 = 0  (true  → false)
        $this->emit('eor x0, x0, #1', 'NOT lógico');
        return 'bool';
    }

    // ── Address-of: &ID ──────────────────────────────────────────────────

    /**
     * Genera la dirección de una variable local del frame.
     * Equivale a tomar la dirección relativa al frame pointer x29.
     *
     * sub x0, x29, #offset  →  x0 = x29 - offset = dirección de la var
     */
    public function visitAddressOf($ctx)
    {
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            $this->emit('mov x0, xzr', '&? → nil (error)');
            return 'pointer';
        }

        $offset = $this->func->getOffset($name);
        $this->emit("sub x0, x29, #$offset", "&$name → dirección en frame");
        return 'pointer';
    }

    // ── Dereference: *expr ────────────────────────────────────────────────

    /**
     * Desreferencia un puntero: carga el valor desde la dirección en x0.
     * Fase 2: asume que el tipo apuntado es int32 (ldr de 64 bits).
     */
    public function visitDereference($ctx)
    {
        $this->visit($ctx->unary());
        // x0 contiene la dirección → cargar el valor apuntado
        $this->emit('ldr x0, [x0]', '*ptr → valor en x0');
        return 'int32';
    }

    // ── Agrupación: (expr) ────────────────────────────────────────────────

    public function visitGroupedExpression($ctx)
    {
        return $this->visit($ctx->expression());
    }

    // ── Arrays (pendiente Fase 3) ─────────────────────────────────────────

    public function visitArrayAccess($ctx)
    {
        $this->emit('mov x0, xzr', 'array access — pendiente Fase 3');
        return 'int32';
    }

    public function visitArrayLiteralExpr($ctx)
    {
        $this->emit('mov x0, xzr', 'array literal — pendiente Fase 3');
        return 'array';
    }
}