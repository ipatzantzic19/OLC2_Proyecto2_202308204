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
 * ✅ CORRECCIÓN FASE 3: Usar w-registers para int32/rune (32-bit)
 *   x++:  ldr w0, [x29, #-offset]  → add w0, w0, #1 → str w0, [x29, #-offset]
 *   x--:  ldr w0, [x29, #-offset]  → sub w0, w0, #1 → str w0, [x29, #-offset]
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
        $type   = $this->func->getType($name);
        
        // ✅ Usar w0 para int32/rune (32-bit)
        $reg = (in_array($type, ['int32', 'rune'])) ? 'w0' : 'x0';
        
        $this->comment("$name++");
        $this->emit("ldr $reg, [x29, #-$offset]", "cargar $name ($type)");
        $this->emit("add $reg, $reg, #1",         "$name + 1");
        $this->emit("str $reg, [x29, #-$offset]", "guardar $name++");
        return null;
    }

    public function visitDecrementStatement($ctx)
    {
        $name = $ctx->ID()->getText();
        if (!$this->func || !$this->func->hasLocal($name)) return null;

        $offset = $this->func->getOffset($name);
        $type   = $this->func->getType($name);
        
        // ✅ Usar w0 para int32/rune (32-bit)
        $reg = (in_array($type, ['int32', 'rune'])) ? 'w0' : 'x0';
        
        $this->comment("$name--");
        $this->emit("ldr $reg, [x29, #-$offset]", "cargar $name ($type)");
        $this->emit("sub $reg, $reg, #1",         "$name - 1");
        $this->emit("str $reg, [x29, #-$offset]", "guardar $name--");
        return null;
    }
}