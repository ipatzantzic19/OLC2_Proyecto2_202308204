<?php

namespace Golampi\Compiler\ARM64\Traits\Declarations;

/**
 * PrescanTrait — Pre-escaneo del bloque para alocación de variables
 *
 * El prescan es una pasada previa sobre el árbol de un bloque de función
 * que registra TODAS las variables locales antes de generar código.
 * Esto permite calcular el tamaño correcto del stack frame desde el inicio,
 * evitando tener que ajustar sp dinámicamente por cada declaración.
 *
 * ¿Por qué es necesario? (concepto de compiladores)
 *   En ARM64 el prólogo de la función reserva espacio con:
 *     sub sp, sp, #FRAME_SIZE
 *   Este valor debe conocerse ANTES de generar cualquier instrucción del cuerpo.
 *   El prescan resuelve este problema haciendo una pasada de "recolección"
 *   sobre el AST antes de la pasada de "generación".
 *
 * Análogo al "primer pasaje" de los ensambladores de dos pasadas:
 *   Pasada 1 (prescan) → recolectar símbolos y calcular offsets
 *   Pasada 2 (visit)   → generar código usando los offsets ya conocidos
 *
 * Tipos reconocidos:
 *   - VarDeclSimpleContext   : var x T
 *   - VarDeclWithInitContext : var x T = expr
 *   - ShortVarDeclContext    : x := expr  (tipo inferido → 'unknown' hasta visit)
 *   - ConstDeclContext       : const x T = expr
 *
 * Bloques anidados (if, for, switch) se escanean recursivamente porque
 * todas las variables locales de la función comparten el mismo stack frame.
 */
trait Prescan
{
    // ── Punto de entrada ─────────────────────────────────────────────────

    /**
     * Escanea un bloque completo (entre llaves { }).
     * Itera sobre todos los hijos internos (excluye los tokens { y }).
     */
    protected function prescanBlock($blockCtx): void
    {
        for ($i = 1; $i < $blockCtx->getChildCount() - 1; $i++) {
            $child = $blockCtx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $this->prescanNode($child);
            }
        }
    }

    // ── Escáner de nodo ───────────────────────────────────────────────────

    /**
     * Analiza un nodo del AST y registra las variables que declara.
     * Para nodos de control de flujo, recursa en sus bloques internos.
     */
    protected function prescanNode($ctx): void
    {
        $class = get_class($ctx);

        // ── var x T ───────────────────────────────────────────────────────
        if (str_ends_with($class, 'VarDeclSimpleContext') ||
            str_ends_with($class, 'VarDeclWithInitContext')) {
            $type = $this->resolveTypeFromCtx($ctx);
            $this->prescanIdList($ctx->idList(), $type);
            return;
        }

        // ── x := expr ─────────────────────────────────────────────────────
        if (str_ends_with($class, 'ShortVarDeclContext')) {
            // El tipo se infiere al visitar la expresión; aquí usamos 'unknown'
            // para reservar el slot. El tipo se actualiza en visitShortVarDecl.
            $this->prescanIdList($ctx->idList(), 'unknown');
            return;
        }

        // ── const x T = expr ──────────────────────────────────────────────
        if (str_ends_with($class, 'ConstDeclContext')) {
            $name = $ctx->ID()->getText();
            if ($this->func && !$this->func->hasLocal($name)) {
                $type = $ctx->type() ? $this->getTypeName($ctx->type()) : 'int32';
                $this->func->allocLocal($name, $type);
            }
            return;
        }

        // ── Recursión en bloques anidados ─────────────────────────────────
        // Necesario para escanear variables dentro de if/for/switch
        for ($i = 0; $i < $ctx->getChildCount(); $i++) {
            $child = $ctx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $childClass = get_class($child);
                if (str_ends_with($childClass, 'BlockContext')) {
                    $this->prescanBlock($child);
                } else {
                    $this->prescanNode($child);
                }
            }
        }
    }

    // ── Helpers internos ──────────────────────────────────────────────────

    /**
     * Registra cada identificador de la lista en el FunctionContext.
     * Si ya existe (re-declaración en scope externo), lo ignora.
     */
    protected function prescanIdList($idList, string $type): void
    {
        if ($idList === null) return;

        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name = $idList->getChild($i)->getText();
            if ($this->func && !$this->func->hasLocal($name)) {
                $this->func->allocLocal($name, $type);
            }
        }
    }

    /**
     * Resuelve el tipo de un contexto de declaración de variable.
     * Extrae el tipo del nodo de tipo del AST.
     */
    private function resolveTypeFromCtx($ctx): string
    {
        try {
            $typeCtx = $ctx->type();
            return $typeCtx ? $this->getTypeName($typeCtx) : 'int32';
        } catch (\Throwable $e) {
            return 'int32';
        }
    }
}