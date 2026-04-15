<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

/**
 * ForWhileTrait — Generación ARM64 para el bucle for-while
 *
 * Soporta la sintaxis:  for cond { stmts }
 *
 * En Golampi no existe la palabra reservada `while`; en su lugar se usa
 * `for` con una única expresión booleana como condición.
 *
 * Estructura ARM64 generada (pre-test loop):
 *
 *   while_start:
 *     [cond code]             // resultado booleano en x0
 *     cbz x0, while_end       // si falso → salir
 *     [body code]
 *     b while_start
 *   while_end:
 *
 * Semántica de break/continue (libro de compiladores Aho et al.):
 *   - break    → salta a while_end
 *   - continue → salta a while_start (re-evalúa la condición)
 *
 * Nota: el label de continue apunta al START (no a un label post),
 * a diferencia del for clásico donde apunta al post-incremento.
 */
trait ForWhile
{
    public function visitForWhile($ctx)
    {
        $startLabel = $this->newLabel('while_start');
        $endLabel   = $this->newLabel('while_end');

        // continue → re-evalúa condición desde el principio
        $this->loopStack[] = ['break' => $endLabel, 'continue' => $startLabel];

        $this->label($startLabel);
        $this->comment('for-while condición');
        $this->visit($ctx->expression());
        $this->emit("cbz x0, $endLabel", 'falso salir');

        $this->generateBlock($ctx->block());

        $this->emit("b $startLabel", 'volver al test');
        $this->label($endLabel);

        array_pop($this->loopStack);
        return null;
    }
}