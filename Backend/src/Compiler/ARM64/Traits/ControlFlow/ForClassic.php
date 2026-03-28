<?php

namespace Golampi\Compiler\ARM64\Traits\ControlFlow;

/**
 * ForClassicTrait — Generación ARM64 para el bucle for clásico
 *
 * Soporta la sintaxis:  for init; cond; post { stmts }
 *
 * Basado en el modelo de registros de activación (Aho et al.):
 *   - La inicialización puede declarar variables con alcance local al bucle
 *   - La condición se evalúa ANTES de cada iteración (pre-test loop)
 *   - El post se ejecuta DESPUÉS del cuerpo
 *
 * Estructura ARM64 generada (patrón: init → test → body → post → test):
 *
 *   [init code]
 *   for_start:
 *     [cond code]           // resultado booleano en x0
 *     cbz x0, for_end       // si falso → salir
 *     [body code]
 *   for_post:               // label de continue → salta aquí
 *     [post code]
 *     b for_start
 *   for_end:                // label de break → salta aquí
 *
 * Los labels for_post y for_end se empujan en $loopStack para que
 * break y continue puedan resolverse correctamente (ver TransferTrait).
 */
trait ForClassic
{
    public function visitForTraditional($ctx)
    {
        $forClause  = $ctx->forClause();
        $startLabel = $this->newLabel('for_start');
        $endLabel   = $this->newLabel('for_end');
        $postLabel  = $this->newLabel('for_post');

        // Registrar labels de break/continue para este nivel de bucle
        $this->loopStack[] = ['break' => $endLabel, 'continue' => $postLabel];

        // ── Inicialización ────────────────────────────────────────────────
        $init = $forClause->forInit();
        if ($init !== null) {
            $this->comment('for init');
            $this->dispatchForInit($init);
        }

        // ── Test de condición (pre-loop) ──────────────────────────────────
        $this->label($startLabel);
        $cond = $forClause->expression();
        if ($cond !== null) {
            $this->comment('for condición');
            $this->visit($cond);
            $this->emit("cbz x0, $endLabel", 'falso → salir del bucle');
        }

        // ── Cuerpo del bucle ─────────────────────────────────────────────
        $this->generateBlock($ctx->block());

        // ── Post-incremento (label de continue) ──────────────────────────
        $this->label($postLabel);
        $post = $forClause->forPost();
        if ($post !== null) {
            $this->comment('for post');
            $this->dispatchForPost($post);
        }

        $this->emit("b $startLabel", 'volver al test');
        $this->label($endLabel);

        array_pop($this->loopStack);
        return null;
    }

    // ── Dispatcher de forInit ─────────────────────────────────────────────

    /**
     * Despacha la cláusula init del for clásico.
     * La cláusula puede ser: varDecl | shortVarDecl | assignment | incDec | vacía
     */
    private function dispatchForInit($initCtx): void
    {
        foreach (['varDeclaration', 'shortVarDeclaration', 'assignment', 'incDecStatement'] as $method) {
            try {
                if (is_callable([$initCtx, $method])) {
                    $node = $initCtx->$method();
                    if ($node !== null) {
                        $this->visit($node);
                        return;
                    }
                }
            } catch (\Throwable $e) {
                // nodo no presente → intentar siguiente
            }
        }
        // Vacío es válido: for ; cond ; post { }
    }

    // ── Dispatcher de forPost ─────────────────────────────────────────────

    /**
     * Despacha la cláusula post del for clásico.
     * Normalmente es i++ o i = i + 1.
     */
    private function dispatchForPost($postCtx): void
    {
        foreach (['assignment', 'incDecStatement'] as $method) {
            try {
                if (is_callable([$postCtx, $method])) {
                    $node = $postCtx->$method();
                    if ($node !== null) {
                        $this->visit($node);
                        return;
                    }
                }
            } catch (\Throwable $e) {
                // vacío permitido
            }
        }
    }

    // ── Visitors requeridos por GolampiBaseVisitor ────────────────────────

    /**
     * visitForInit y visitForPost deben ser public (herencia de BaseVisitor).
     * Delegamos a los dispatchers privados para mantener la lógica encapsulada.
     */
    public function visitForInit($ctx): void
    {
        $this->dispatchForInit($ctx);
    }

    public function visitForPost($ctx): void
    {
        $this->dispatchForPost($ctx);
    }
}