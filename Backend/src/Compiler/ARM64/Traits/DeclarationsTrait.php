<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * DeclarationsTrait — Fase 2
 *
 * Generación de código ARM64 para declaraciones:
 *   var, :=, const
 *
 * Cambios Fase 2:
 *   - storeDefault diferencia int32 vs float32
 *   - storeExpr diferencia int32 vs float32 al momento de str
 *   - prescanBlock reconoce float32 para asignar tipo correcto en allocLocal
 */
trait DeclarationsTrait
{
    // ═════════════════════════════════════════════════════════════════════
    //  PRE-ESCANER
    // ═════════════════════════════════════════════════════════════════════

    protected function prescanBlock($blockCtx): void
    {
        for ($i = 1; $i < $blockCtx->getChildCount() - 1; $i++) {
            $child = $blockCtx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $this->prescanNode($child);
            }
        }
    }

    protected function prescanNode($ctx): void
    {
        $class = get_class($ctx);

        if (str_ends_with($class, 'VarDeclSimpleContext') ||
            str_ends_with($class, 'VarDeclWithInitContext')) {
            $typeCtx = $ctx->type();
            $type    = $typeCtx ? $this->getTypeName($typeCtx) : 'int32';
            $this->prescanIds($ctx->idList(), $type);
            return;
        }

        if (str_ends_with($class, 'ShortVarDeclContext')) {
            $this->prescanIds($ctx->idList(), 'unknown');
            return;
        }

        if (str_ends_with($class, 'ConstDeclContext')) {
            $name = $ctx->ID()->getText();
            if ($this->func && !$this->func->hasLocal($name)) {
                $typeCtx = $ctx->type();
                $type    = $typeCtx ? $this->getTypeName($typeCtx) : 'int32';
                $this->func->allocLocal($name, $type);
            }
            return;
        }

        // Recursión en bloques anidados
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

    protected function prescanIds($idList, string $type): void
    {
        if ($idList === null) return;
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name = $idList->getChild($i)->getText();
            if ($this->func && !$this->func->hasLocal($name)) {
                $this->func->allocLocal($name, $type);
            }
        }
    }

    // ═════════════════════════════════════════════════════════════════════
    //  VISITORS DE DECLARACIÓN
    // ═════════════════════════════════════════════════════════════════════

    public function visitDeclaration($ctx)  { return $this->visitChildren($ctx); }
    public function visitStatement($ctx)    { return $this->visitChildren($ctx); }

    public function visitFuncDeclSingleReturn($ctx) { return null; }
    public function visitFuncDeclMultiReturn($ctx)  { return null; }

    // ─── var x T ──────────────────────────────────────────────────────────

    public function visitVarDeclSimple($ctx)
    {
        $typeCtx = $ctx->type();
        $type    = $this->getTypeName($typeCtx);
        $idList  = $ctx->idList();
        $line    = $ctx->getStart()->getLine();
        $col     = $ctx->getStart()->getCharPositionInLine();

        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name   = $idList->getChild($i)->getText();
            $offset = $this->allocVar($name, $type, $line, $col);
            if ($offset === null) continue;

            $this->comment("var $name $type (default)");
            $this->storeDefault($type, $offset);
            $this->addSymbol($name, $type, $this->func->name, null, $line, $col);
        }
        return null;
    }

    // ─── var x T = expr ───────────────────────────────────────────────────

    public function visitVarDeclWithInit($ctx)
    {
        $typeCtx  = $ctx->type();
        $type     = $this->getTypeName($typeCtx);
        $idList   = $ctx->idList();
        $exprList = $ctx->expressionList();
        $line     = $ctx->getStart()->getLine();
        $col      = $ctx->getStart()->getCharPositionInLine();

        $exprCount = 0;
        for ($i = 0; $i < $exprList->getChildCount(); $i += 2) $exprCount++;

        $idx = 0;
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name   = $idList->getChild($i)->getText();
            $offset = $this->allocVar($name, $type, $line, $col);
            if ($offset === null) { $idx++; continue; }

            if ($idx < $exprCount) {
                $exprCtx  = $exprList->getChild($idx * 2);
                $this->comment("var $name $type = expr");
                $exprType = $this->visit($exprCtx);

                // Conversión automática si los tipos no coinciden
                $exprType = $this->coerceIfNeeded($type, $exprType ?? $type);
                $this->storeResult($type, $offset);
                $this->func->setType($name, $exprType ?? $type);
            } else {
                $this->storeDefault($type, $offset);
            }
            $this->addSymbol($name, $type, $this->func->name, null, $line, $col);
            $idx++;
        }
        return null;
    }

    // ─── x := expr ────────────────────────────────────────────────────────

    public function visitShortVarDecl($ctx)
    {
        $idList   = $ctx->idList();
        $exprList = $ctx->expressionList();
        $line     = $ctx->getStart()->getLine();
        $col      = $ctx->getStart()->getCharPositionInLine();

        $exprCount = 0;
        for ($i = 0; $i < $exprList->getChildCount(); $i += 2) $exprCount++;

        $idx = 0;
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name = $idList->getChild($i)->getText();

            if ($idx < $exprCount) {
                $exprCtx  = $exprList->getChild($idx * 2);
                $this->comment("$name := expr");
                $exprType = $this->visit($exprCtx) ?? 'int32';

                $offset = $this->allocVar($name, $exprType, $line, $col);
                if ($offset !== null) {
                    $this->storeResult($exprType, $offset);
                    $this->func->setType($name, $exprType);
                    $this->addSymbol($name, $exprType, $this->func->name, null, $line, $col);
                }
            }
            $idx++;
        }
        return null;
    }

    // ─── const x T = expr ────────────────────────────────────────────────

    public function visitConstDecl($ctx)
    {
        $name    = $ctx->ID()->getText();
        $typeCtx = $ctx->type();
        $type    = $this->getTypeName($typeCtx);
        $line    = $ctx->getStart()->getLine();
        $col     = $ctx->getStart()->getCharPositionInLine();

        $offset = $this->allocVar($name, $type, $line, $col);
        if ($offset === null) return null;

        $this->comment("const $name $type");
        $exprType = $this->visit($ctx->expression());
        $this->coerceIfNeeded($type, $exprType ?? $type);
        $this->storeResult($type, $offset);
        $this->addSymbol($name, $type . ' (const)', $this->func->name, null, $line, $col);
        return null;
    }

    // ═════════════════════════════════════════════════════════════════════
    //  HELPERS INTERNOS
    // ═════════════════════════════════════════════════════════════════════

    /**
     * Almacena el resultado de una expresión (x0 o s0) en el frame.
     * Usa str x0 para int/bool/string/rune/pointer,
     * y str s0 para float32.
     */
    private function storeResult(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("str s0, [x29, #-$offset]",  'guardar float32');
        } else {
            $this->emit("str x0, [x29, #-$offset]",  "guardar $type");
        }
    }

    /**
     * Conversión automática de tipo entre el tipo declarado y el tipo de la expresión.
     * Si hay conversión, emite la instrucción y retorna el tipo final.
     *
     * @param string $declaredType  Tipo declarado de la variable
     * @param string $exprType      Tipo del resultado de la expresión
     * @return string               Tipo final tras conversión
     */
    private function coerceIfNeeded(string $declaredType, string $exprType): string
    {
        if ($declaredType === $exprType) return $exprType;

        if ($declaredType === 'float32' && ($exprType === 'int32' || $exprType === 'rune')) {
            // Promover int32 → float32
            $this->emitIntToFloat();
            return 'float32';
        }
        if ($declaredType === 'int32' && $exprType === 'float32') {
            // Truncar float32 → int32
            $this->emitFloatToInt();
            return 'int32';
        }
        // Otros casos: sin conversión (puede ser un error semántico ignorado)
        return $exprType;
    }
}