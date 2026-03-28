<?php

namespace Golampi\Compiler\ARM64\Traits\Assignments;

/**
 * PointerAssignment — Generación ARM64 para asignaciones a través de puntero
 *
 * Soporta: *ptr = expr
 *
 * Semántica (Aho et al. — paso por referencia):
 *   Un parámetro puntero *T recibe la dirección de la variable original.
 *   La asignación *ptr = expr escribe en la dirección apuntada, modificando
 *   la variable original sin necesidad de retornarla.
 *
 * Generación ARM64:
 *   1. Cargar ptr desde el frame → x1 (contiene la dirección de la var original)
 *   2. Evaluar expr → x0
 *   3. str x0, [x1]   → escribir en la dirección apuntada
 *
 * Convención AArch64:
 *   Las variables locales y parámetros viven en [x29 - offset].
 *   Un puntero a la variable x con offset N tiene valor: x29 - N
 *   (calculado en visitAddressOf con: sub x0, x29, #N)
 *   La desreferencia escribe en esa dirección: str x0, [x1]
 */
trait PointerAssignment
{
    public function visitPointerAssignment($ctx)
    {
        $name = $ctx->ID()->getText();
        $op   = $ctx->assignOp()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Puntero '$name' no declarado", $line, $col);
            return null;
        }

        $ptrOffset = $this->func->getOffset($name);
        $this->comment("*$name $op expr");

        if ($op === '=') {
            // ── *ptr = expr ───────────────────────────────────────────────
            // Cargar la dirección almacenada en ptr → x1
            $this->emit("ldr x1, [x29, #-$ptrOffset]", "cargar dirección de *$name → x1");
            // Evaluar el nuevo valor → x0
            $this->visit($ctx->expression());
            // Escribir en la dirección apuntada
            $this->emit('str x0, [x1]', "*$name ← valor (store indirecto)");

        } else {
            // ── *ptr OP= expr ─────────────────────────────────────────────
            // Cargar dirección → x2 (preservar para el store final)
            $this->emit("ldr x2, [x29, #-$ptrOffset]", "cargar dirección de *$name → x2");
            // Leer valor actual a través del puntero → x1 (lhs)
            $this->emit('ldr x1, [x2]', "leer *$name → x1 (lhs)");
            $this->pushStack(); // x1 → stack (no tenemos sub sp para x1 directo)
            // Evaluar rhs → x0
            $this->visit($ctx->expression());
            $this->emit('ldr x1, [sp]', 'lhs ← stack');
            $this->emit('add sp, sp, #16');
            // Aplicar operación
            $scalar = match ($op) {
                '+=' => '+', '-=' => '-', '*=' => '*', '/=' => '/', default => '+',
            };
            $this->emitBinaryOp($scalar);
            // Recargar dirección y escribir resultado
            $this->emit("ldr x1, [x29, #-$ptrOffset]", "recargar dirección de *$name");
            $this->emit('str x0, [x1]', "*$name ← resultado (store indirecto)");
        }

        return null;
    }
}