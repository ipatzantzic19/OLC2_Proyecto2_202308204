<?php

namespace Golampi\Compiler\ARM64\Traits\Assignments;

/**
 * IncrementDecrement — Generación ARM64 para ++ y --
 *
 * Soporta las sentencias de incremento y decremento postfijo:
 *   x++   (equivale a: x = x + 1)
 *   x--   (equivale a: x = x - 1)
 *
 * En Golampi (igual que en Go), ++ y -- son sentencias, no expresiones.
 * No pueden usarse en el lado derecho de una asignación: b = a++ es inválido.
 *
 * Restricción de tipo (enunciado sección 3.3.10):
 *   Solo válidos para int32 y rune. No aplican a float32, bool ni string.
 *
 * Generación ARM64:
 *   x++:  ldr x0, [x29, #-offset]  → add x0, x0, #1 → str x0, [x29, #-offset]
 *   x--:  ldr x0, [x29, #-offset]  → sub x0, x0, #1 → str x0, [x29, #-offset]
 *
 * ARM64 no tiene instrucciones inc/dec dedicadas (a diferencia de x86).
 * Se usan add/sub con inmediato #1, que el procesador maneja eficientemente.
 */
trait IncrementDecrement
{
    public function visitIncrementStatement($ctx)
    {
        $name = $ctx->ID()->getText();
        if (!$this->func || !$this->func->hasLocal($name)) return null;

        $offset = $this->func->getOffset($name);
        $this->comment("$name++");
        $this->emit("ldr x0, [x29, #-$offset]", "cargar $name");
        $this->emit('add x0, x0, #1',            "$name + 1");
        $this->emit("str x0, [x29, #-$offset]",  "guardar $name++");
        return null;
    }

    public function visitDecrementStatement($ctx)
    {
        $name = $ctx->ID()->getText();
        if (!$this->func || !$this->func->hasLocal($name)) return null;

        $offset = $this->func->getOffset($name);
        $this->comment("$name--");
        $this->emit("ldr x0, [x29, #-$offset]", "cargar $name");
        $this->emit('sub x0, x0, #1',            "$name - 1");
        $this->emit("str x0, [x29, #-$offset]",  "guardar $name--");
        return null;
    }
}