<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * ControlFlowTrait
 *
 * Responsabilidad: generar código ARM64 para todas las estructuras de
 * control de flujo: if/else if/else, for (3 variantes), switch/case/default,
 * break, continue y return.
 *
 * Estado que usa de la clase:
 *   array  $loopStack   → stack de etiquetas break/continue
 */
trait ControlFlowTrait
{
    // ─── if / else if / else ─────────────────────────────────────────────────

    public function visitIfElseIfElse($ctx)
    {
        $conditions = $ctx->expression();
        $blocks     = $ctx->block();
        $endLabel   = $this->newLabel('if_end');
        $n          = count($conditions);

        for ($i = 0; $i < $n; $i++) {
            $isLastCond = ($i === $n - 1);
            $hasElse    = (count($blocks) > $n);
            $nextLabel  = ($isLastCond && !$hasElse) ? $endLabel : $this->newLabel('else');

            $this->comment('if condición #' . ($i + 1));
            $this->visit($conditions[$i]);
            $this->emit("cbz x0, $nextLabel",       'saltar si falso');

            $this->generateBlock($blocks[$i]);
            $this->emit("b $endLabel");

            if ($nextLabel !== $endLabel) {
                $this->label($nextLabel);
            }
        }

        // Bloque else (opcional)
        if (count($blocks) > $n) {
            $this->comment('else');
            $this->generateBlock($blocks[$n]);
        }

        $this->label($endLabel);
        return null;
    }

    // ─── for init; cond; post { } ─────────────────────────────────────────────

    public function visitForTraditional($ctx)
    {
        $forClause  = $ctx->forClause();
        $startLabel = $this->newLabel('for_start');
        $endLabel   = $this->newLabel('for_end');
        $postLabel  = $this->newLabel('for_post');

        $this->loopStack[] = ['break' => $endLabel, 'continue' => $postLabel];

        // Init
        $init = $forClause->forInit();
        if ($init !== null) {
            $this->comment('for init');
            $this->visitForInit($init);
        }

        // Condición
        $this->label($startLabel);
        $cond = $forClause->expression();
        if ($cond !== null) {
            $this->comment('for condición');
            $this->visit($cond);
            $this->emit("cbz x0, $endLabel",        'saltar si falso');
        }

        // Cuerpo
        $this->generateBlock($ctx->block());

        // Post (increment)
        $this->label($postLabel);
        $post = $forClause->forPost();
        if ($post !== null) {
            $this->comment('for post');
            $this->visitForPost($post);
        }

        $this->emit("b $startLabel");
        $this->label($endLabel);
        array_pop($this->loopStack);
        return null;
    }

    /**
     * Genera el código de la cláusula init del for clásico.
     * Debe ser public porque GolampiBaseVisitor lo declara public.
     */
    public function visitForInit($initCtx): void
    {
        if (method_exists($initCtx, 'varDeclaration') && $initCtx->varDeclaration()) {
            $this->visit($initCtx->varDeclaration());
        } elseif (method_exists($initCtx, 'shortVarDeclaration') && $initCtx->shortVarDeclaration()) {
            $this->visit($initCtx->shortVarDeclaration());
        } elseif (method_exists($initCtx, 'assignment') && $initCtx->assignment()) {
            $this->visit($initCtx->assignment());
        } elseif (method_exists($initCtx, 'incDecStatement') && $initCtx->incDecStatement()) {
            $this->visit($initCtx->incDecStatement());
        }
    }

    /**
     * Genera el código de la cláusula post del for clásico.
     * Debe ser public porque GolampiBaseVisitor lo declara public.
     */
    public function visitForPost($postCtx): void
    {
        if (method_exists($postCtx, 'assignment') && $postCtx->assignment()) {
            $this->visit($postCtx->assignment());
        } elseif (method_exists($postCtx, 'incDecStatement') && $postCtx->incDecStatement()) {
            $this->visit($postCtx->incDecStatement());
        }
    }

    // ─── for cond { }  (estilo while) ────────────────────────────────────────

    public function visitForWhile($ctx)
    {
        $startLabel = $this->newLabel('while_start');
        $endLabel   = $this->newLabel('while_end');

        $this->loopStack[] = ['break' => $endLabel, 'continue' => $startLabel];

        $this->label($startLabel);
        $this->visit($ctx->expression());
        $this->emit("cbz x0, $endLabel");
        $this->generateBlock($ctx->block());
        $this->emit("b $startLabel");
        $this->label($endLabel);

        array_pop($this->loopStack);
        return null;
    }

    // ─── for { }  (bucle infinito) ───────────────────────────────────────────

    public function visitForInfinite($ctx)
    {
        $startLabel = $this->newLabel('inf_start');
        $endLabel   = $this->newLabel('inf_end');

        $this->loopStack[] = ['break' => $endLabel, 'continue' => $startLabel];

        $this->label($startLabel);
        $this->generateBlock($ctx->block());
        $this->emit("b $startLabel");
        $this->label($endLabel);

        array_pop($this->loopStack);
        return null;
    }

    // ─── switch expr { case v: default: } ────────────────────────────────────

    public function visitSwitchStatement($ctx)
    {
        $endLabel = $this->newLabel('sw_end');

        $this->comment('switch — evaluar expresión');
        $this->visit($ctx->expression());
        // x19 es callee-saved: seguro mientras se generan los comparisons
        $this->emit('mov x19, x0',              'valor del switch → x19');

        $cases    = $ctx->caseClause();
        $default_ = $ctx->defaultClause();

        $defaultLabel = $endLabel;
        if ($default_ !== null) {
            $defaultLabel = $this->newLabel('sw_default');
        }

        // Generar labels para cada case
        $caseLabels = [];
        foreach ($cases as $k => $_case) {
            $caseLabels[$k] = $this->newLabel('sw_case');
        }

        // Tabla de saltos: comparar y saltar
        foreach ($cases as $k => $case) {
            $exprList = $case->expressionList();
            for ($i = 0; $i < $exprList->getChildCount(); $i += 2) {
                $this->visit($exprList->getChild($i));
                $this->emit("cmp x19, x0");
                $this->emit("b.eq {$caseLabels[$k]}");
            }
        }
        $this->emit("b $defaultLabel");

        // Cuerpos de cada case
        foreach ($cases as $k => $case) {
            $this->label($caseLabels[$k]);
            for ($i = 0; $i < $case->getChildCount(); $i++) {
                $child = $case->getChild($i);
                if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                    $this->visit($child);
                }
            }
            $this->emit("b $endLabel");
        }

        // Cuerpo del default
        if ($default_ !== null) {
            $this->label($defaultLabel);
            for ($i = 0; $i < $default_->getChildCount(); $i++) {
                $child = $default_->getChild($i);
                if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                    $this->visit($child);
                }
            }
        }

        $this->label($endLabel);
        return null;
    }

    // ─── break ───────────────────────────────────────────────────────────────

    public function visitBreakStatement($ctx)
    {
        if (empty($this->loopStack)) {
            $this->addError('Semántico', "'break' fuera de bucle o switch",
                $ctx->getStart()->getLine(), 0);
            return null;
        }
        $label = end($this->loopStack)['break'];
        $this->emit("b $label",  'break');
        return null;
    }

    // ─── continue ────────────────────────────────────────────────────────────

    public function visitContinueStatement($ctx)
    {
        if (empty($this->loopStack)) {
            $this->addError('Semántico', "'continue' fuera de bucle",
                $ctx->getStart()->getLine(), 0);
            return null;
        }
        $label = end($this->loopStack)['continue'];
        $this->emit("b $label",  'continue');
        return null;
    }

    // ─── return ──────────────────────────────────────────────────────────────

    public function visitReturnStatement($ctx)
    {
        $exprList = $ctx->expressionList();
        if ($exprList !== null) {
            $this->visit($exprList->getChild(0));  // valor → x0
        } else {
            $this->emit('mov x0, #0');
        }
        $epilogue = $this->func ? $this->func->epilogueLabel : '.epilogue_unknown';
        $this->emit("b $epilogue",  'return');
        return null;
    }

    // ─── statement / block passthrough ───────────────────────────────────────

    public function visitExpressionStatement($ctx)
    {
        return $this->visit($ctx->expression());
    }

    public function visitBlock($ctx)
    {
        $this->generateBlock($ctx);
        return null;
    }
}