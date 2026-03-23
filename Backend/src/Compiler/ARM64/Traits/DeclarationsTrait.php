<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * DeclarationsTrait
 *
 * Responsabilidad: generar código ARM64 para declaraciones de variables
 * (var, :=, const) y el pre-escaner de bloques que pre-asigna slots
 * en el stack frame antes de generar el prólogo.
 */
trait DeclarationsTrait
{
    // ═════════════════════════════════════════════════════════════════════
    //  PRE-ESCANER  — recorre el AST antes del prólogo para conocer el
    //  tamaño total del frame y emitir "sub sp, sp, #N" correctamente.
    // ═════════════════════════════════════════════════════════════════════

    protected function prescanBlock($blockCtx): void
    {
        // Los hijos 0 y getChildCount()-1 son '{' y '}'
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

        // VarDeclSimple / VarDeclWithInit → tipo conocido
        if (str_ends_with($class, 'VarDeclSimpleContext') ||
            str_ends_with($class, 'VarDeclWithInitContext')) {
            $typeCtx = $ctx->type();
            $type    = $typeCtx ? $this->getTypeName($typeCtx) : 'int32';
            $this->prescanIds($ctx->idList(), $type);
            return;
        }

        // ShortVarDecl → tipo se infiere después
        if (str_ends_with($class, 'ShortVarDeclContext')) {
            $this->prescanIds($ctx->idList(), 'unknown');
            return;
        }

        // ConstDecl → un solo ID
        if (str_ends_with($class, 'ConstDeclContext')) {
            $name = $ctx->ID()->getText();
            if ($this->func && !$this->func->hasLocal($name)) {
                $typeCtx = $ctx->type();
                $type    = $typeCtx ? $this->getTypeName($typeCtx) : 'int32';
                $this->func->allocLocal($name, $type);
            }
            return;
        }

        // Recursión: para if, for, etc. escanear sub-bloques
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

    /** Las declaraciones de funciones ya se procesaron en visitProgram */
    public function visitFuncDeclSingleReturn($ctx) { return null; }
    public function visitFuncDeclMultiReturn($ctx)  { return null; }

    // ─── var x int32 ──────────────────────────────────────────────────────

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

    // ─── var x int32 = expr ───────────────────────────────────────────────

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
                $this->func->setType($name, $exprType ?? $type);
                $this->emit("str x0, [x29, #-$offset]");
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
                    $this->emit("str x0, [x29, #-$offset]");
                    $this->func->setType($name, $exprType);
                    $this->addSymbol($name, $exprType, $this->func->name, null, $line, $col);
                }
            }
            $idx++;
        }
        return null;
    }

    // ─── const x int32 = expr ────────────────────────────────────────────

    /** Fase 1: const se trata como variable inmutable en el stack. */
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
        $this->visit($ctx->expression());
        $this->emit("str x0, [x29, #-$offset]");
        $this->addSymbol($name, $type . ' (const)', $this->func->name, null, $line, $col);

        return null;
    }
}