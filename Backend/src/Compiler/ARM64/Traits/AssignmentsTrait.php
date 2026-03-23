<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * AssignmentsTrait
 *
 * Responsabilidad: generar código ARM64 para todas las formas de asignación:
 * simple (=), compuesta (+=, -=, *=, /=), incremento (++) y decremento (--).
 */
trait AssignmentsTrait
{
    // ─── x = expr  /  x += expr  etc. ───────────────────────────────────────

    public function visitSimpleAssignment($ctx)
    {
        $name = $ctx->ID()->getText();
        $op   = $ctx->assignOp()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            return null;
        }

        $offset = $this->func->getOffset($name);

        if ($op === '=') {
            $this->comment("$name = expr");
            $this->visit($ctx->expression());
            $this->emit("str x0, [x29, #-$offset]");
        } else {
            // x op= rhs  →  carga lhs, evalúa rhs, aplica op, guarda
            $this->comment("$name $op expr");
            $this->emit("ldr x9, [x29, #-$offset]",  "cargar $name");
            $this->emit('sub sp, sp, #16');
            $this->emit('str x9, [sp]',               'apilar lhs');
            $this->visit($ctx->expression());           // rhs → x0
            $this->emit('ldr x1, [sp]',               'recuperar lhs → x1');
            $this->emit('add sp, sp, #16');

            $scalar = match ($op) {
                '+=' => '+', '-=' => '-', '*=' => '*', '/=' => '/', default => '+'
            };
            $this->emitBinaryOp($scalar);
            $this->emit("str x0, [x29, #-$offset]");
        }
        return null;
    }

    /** ID '[' expr ']' assignOp expr  — Fase 3 (arrays) */
    public function visitArrayAssignment($ctx)   { return null; }

    /** *ptr = expr  — Fase 3 (punteros) */
    public function visitPointerAssignment($ctx) { return null; }

    // ─── x++ ────────────────────────────────────────────────────────────────

    public function visitIncrementStatement($ctx)
    {
        $name = $ctx->ID()->getText();
        if (!$this->func || !$this->func->hasLocal($name)) return null;

        $offset = $this->func->getOffset($name);
        $this->comment("$name++");
        $this->emit("ldr x0, [x29, #-$offset]");
        $this->emit('add x0, x0, #1');
        $this->emit("str x0, [x29, #-$offset]");
        return null;
    }

    // ─── x-- ────────────────────────────────────────────────────────────────

    public function visitDecrementStatement($ctx)
    {
        $name = $ctx->ID()->getText();
        if (!$this->func || !$this->func->hasLocal($name)) return null;

        $offset = $this->func->getOffset($name);
        $this->comment("$name--");
        $this->emit("ldr x0, [x29, #-$offset]");
        $this->emit('sub x0, x0, #1');
        $this->emit("str x0, [x29, #-$offset]");
        return null;
    }
}