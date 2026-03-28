<?php

namespace Golampi\Compiler\ARM64\Traits\Assignments;

/**
 * ArrayAssignment — Generación ARM64 para asignaciones a arreglos
 *
 * Soporta: a[i] = expr  /  m[i][j] = expr
 *
 * Fase 3 implementará la lógica completa de indexación dinámica.
 * En Fase 2 se emite un stub para no romper el visitor.
 *
 * Concepto de compiladores (Aho et al. — generación de lvalues):
 *   Una asignación a arreglo requiere calcular la dirección del elemento:
 *     addr(a[i]) = base_addr(a) + i * sizeof(elem)
 *   Para arrays multidimensionales (row-major):
 *     addr(m[i][j]) = base + (i * cols + j) * sizeof(elem)
 *   Estas fórmulas se generan en la Fase 3 (ArrayCodegenTrait).
 */
trait ArrayAssignment
{
    public function visitArrayAssignment($ctx)
    {
        // Fase 3: generación completa de indexación ARM64
        // Por ahora registrar el error solo si la variable no existe
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if ($this->func && !$this->func->hasLocal($name) && !$this->func->hasArray($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
        }
        // La generación de código para indexación dinámica se implementa en Fase 3
        return null;
    }
}