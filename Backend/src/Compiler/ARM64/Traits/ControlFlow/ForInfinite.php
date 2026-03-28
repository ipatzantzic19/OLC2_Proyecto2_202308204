<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

/**
 * ForInfiniteTrait — Generación ARM64 para el bucle infinito
 *
 * Soporta la sintaxis:  for { stmts }
 *
 * Un bucle infinito NO tiene condición de guarda. El cuerpo se ejecuta
 * indefinidamente hasta que una sentencia de transferencia (break, return)
 * interrumpa el flujo.
 *
 * Estructura ARM64 generada:
 *
 *   inf_start:
 *     [body code]
 *     b inf_start           // salto incondicional → loop forever
 *   inf_end:                // destino de break
 *
 * Semántica (Aho et al. — control flow graph):
 *   - El bucle crea un bloque básico que se conecta a sí mismo.
 *   - break    → arista de salida al bloque post-bucle (inf_end)
 *   - continue → arista hacia inf_start (misma semántica que while)
 *   - return   → arista de salida al epílogo de la función
 *
 * Sin break/return, el grafo de flujo de control no tiene salida del bucle
 * (loop infinito real), lo cual es intencionado según el enunciado.
 */
trait ForInfinite
{
    public function visitForInfinite($ctx)
    {
        $startLabel = $this->newLabel('inf_start');
        $endLabel   = $this->newLabel('inf_end');

        $this->loopStack[] = ['break' => $endLabel, 'continue' => $startLabel];

        $this->label($startLabel);
        $this->comment('for infinito — cuerpo');
        $this->generateBlock($ctx->block());

        $this->emit("b $startLabel", 'bucle infinito');
        $this->label($endLabel);

        array_pop($this->loopStack);
        return null;
    }
}