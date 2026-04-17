<?php

namespace Golampi\Compiler\ARM64\Traits\Literals;

/**
 * ScalarLiteral — Generación ARM64 para literales escalares simples
 *
 * Implementa los literales restantes que no requieren pools ni cálculos:
 *
 *   true   → mov x0, #1    (bool representado como 1)
 *   false  → mov x0, xzr   (bool representado como 0)
 *   nil    → mov x0, xzr   (puntero nulo / ausencia de valor)
 *
 * Representación de bool en ARM64:
 *   Los valores booleanos se almacenan como enteros de 64 bits donde
 *   0 = false y cualquier valor != 0 = true (convención C estándar).
 *   Las instrucciones cset generan siempre 0 o 1 (no valores arbitrarios).
 *
 * Representación de nil:
 *   nil es el puntero nulo y el valor "ausente" de Golampi.
 *   Cualquier operación sobre nil debe generar un error semántico,
 *   lo que se valida en los traits de expresiones.
 *
 * También incluye visitInnerArrayLiteral como passthrough: los literales
 * de arrays internos ( {1,2,3} ) se procesan en el contexto del array
 * contenedor (Fase 3), no de forma independiente.
 */
trait ScalarLiteral
{
    public function visitTrueLiteral($ctx)
    {
        $this->emit('mov w0, #1', 'bool true = 1 (32-bit)');
        return 'bool';
    }

    public function visitFalseLiteral($ctx)
    {
        $this->emit('mov w0, wzr', 'bool false = 0 (32-bit)');
        return 'bool';
    }

    public function visitNilLiteral($ctx)
    {
        $this->emit('mov x0, xzr', 'nil = 0 (puntero nulo)');
        return 'nil';
    }

    /**
     * Passthrough para literales de array interno { e1, e2, ... }.
     * El contexto contenedor (FixedArrayLiteralNode) lo procesa en Fase 3.
     */
    public function visitInnerArrayLiteral($ctx)
    {
        $this->emit('mov x0, xzr', 'inner array literal — pendiente Fase 3');
        return 'array';
    }
}