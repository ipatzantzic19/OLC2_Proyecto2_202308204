<?php

namespace Golampi\Compiler\ARM64\Traits\Helpers;

/**
 * IdentifierVisitor — Generación ARM64 para lectura de variables
 *
 * Implementa visitIdentifier: genera el código para cargar el valor
 * de una variable local al registro correcto según su tipo.
 *
 * Basado en descriptores de registro (Aho et al.):
 *   Un descriptor de dirección mapea cada variable a su ubicación actual.
 *   Aquí la ubicación siempre es el stack frame [x29 - offset].
 *
 * ✅ CORRECCIÓN FASE 3: Distinción por tamaño de registro
 *   int32/bool/rune → ldr x0, [x29, #-offset]   (64-bit registro per AArch64 standard)
 *   float32 → ldr s0, [x29, #-offset]   (32-bit SIMD)
 *   puntero/string/array → ldr x0, [x29, #-offset]   (64-bit registro)
 *
 * Error semántico:
 *   Si la variable no está declarada en el scope actual, se reporta
 *   el error y se emite mov x0, xzr para mantener la coherencia del
 *   flujo de generación (recuperación de errores).
 */
trait IdentifierVisitor
{
    public function visitIdentifier($ctx)
    {
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            $this->emit('mov x0, xzr', "? ($name no declarada)");
            return 'int32';
        }

        $offset = $this->func->getOffset($name);
        $type   = $this->func->getType($name);

        if ($type === 'float32') {
            $this->emit("ldr s0, [x29, #-$offset]", "$name (float32)");
        } elseif (in_array($type, ['int32', 'bool', 'rune'])) {
            // ✅ Usar x0 (64-bit) para tipos enteros per AArch64 standard
            $this->emit("ldr x0, [x29, #-$offset]", "$name ($type - 64-bit)");
        } else {
            // Puntero, string, array → x0 (64-bit)
            $this->emit("ldr x0, [x29, #-$offset]", "$name ($type - 64-bit)");
        }
        return $type;
    }
}