<?php

namespace Golampi\Traits;

use Golampi\Runtime\Environment;
use Golampi\Runtime\Value;

/**
 * Trait para visitar sentencias del AST.
 * Implementa hoisting de funciones en visitProgram.
 */
trait StatementVisitor
{
    // =========================================================
    //  PROGRAMA (dos pases: hoisting → ejecución)
    // =========================================================

    public function visitProgram($context)
    {
        $mainDecl = null;

        // ── PASE 1: Hoisting ─────────────────────────────────────────
        // Registrar todas las funciones de usuario ANTES de ejecutar nada
        for ($i = 0; $i < $context->getChildCount() - 1; $i++) {
            $child = $context->getChild($i);
            if (!($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext)) {
                continue;
            }

            if (!method_exists($child, 'functionDeclaration')) {
                continue;
            }

            $funcDecl = $child->functionDeclaration();
            if ($funcDecl === null) {
                continue;
            }

            $funcName = $funcDecl->ID()->getText();

            if ($funcName === 'main') {
                $mainDecl = $funcDecl;
            } else {
                $this->registerUserFunction($funcDecl);
            }
        }

        // ── PASE 2: Declaraciones globales (var / const) ─────────────
        for ($i = 0; $i < $context->getChildCount() - 1; $i++) {
            $child = $context->getChild($i);
            if (!($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext)) {
                continue;
            }

            // Saltar declaraciones de función (ya procesadas en pase 1)
            if (method_exists($child, 'functionDeclaration')
                && $child->functionDeclaration() !== null
            ) {
                continue;
            }

            $this->visit($child);
        }

        // ── PASE 3: Ejecutar main ────────────────────────────────────
        if ($mainDecl !== null) {
            $this->executeMain($mainDecl);
        }

        return null;
    }

    // =========================================================
    //  DECLARACIONES DE FUNCIÓN (no-op: ya manejadas en hoisting)
    // =========================================================

    public function visitFuncDeclSingleReturn($context)
    {
        // Manejado íntegramente en visitProgram (hoisting + executeMain)
        return null;
    }

    public function visitFuncDeclMultiReturn($context)
    {
        // Manejado íntegramente en visitProgram (hoisting)
        return null;
    }

    // =========================================================
    //  DECLARACIÓN
    // =========================================================

    public function visitDeclaration($context)
    {
        return $this->visitChildren($context);
    }

    // =========================================================
    //  BLOQUE
    // =========================================================

    public function visitBlock($context)
    {
        $parentEnv = $this->environment;
        $this->environment = new Environment($parentEnv);

        $currentScope = $this->getCurrentScopeName();
        $createScope  = !in_array(
            $currentScope,
            ['for', 'switch', 'function:main', 'if-block', 'else-block'],
            true
        );

        // Para scopes de función de usuario no crear scope extra
        if (str_starts_with($currentScope, 'function:')) {
            $createScope = false;
        }

        if ($createScope) {
            $this->enterScope('block');
        }

        try {
            if ($context->getChildCount() > 2) {
                for ($i = 1; $i < $context->getChildCount() - 1; $i++) {
                    $child = $context->getChild($i);
                    if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                        $this->visit($child);
                    }
                }
            }
        } finally {
            if ($createScope) {
                $this->exitScope();
            }
            $this->environment = $parentEnv;
        }

        return null;
    }

    // =========================================================
    //  EXPRESIÓN STATEMENT
    // =========================================================

    public function visitExpressionStatement($context)
    {
        return $this->visit($context->expression());
    }

    // =========================================================
    //  LLAMADA A FUNCIÓN
    // =========================================================

    /**
     * Visita una llamada a función (builtins y funciones de usuario).
     */
    public function visitFunctionCall($context)
    {
        $ids = $context->ID();

        // Determinar nombre: fmt.Println vs len(x)
        if (is_array($ids) && count($ids) >= 2) {
            $funcName = $ids[0]->getText() . '.' . $ids[1]->getText();
        } else {
            $funcName = (is_array($ids) ? $ids[0] : $ids)->getText();
        }

        $line   = $context->getStart()->getLine();
        $column = $context->getStart()->getCharPositionInLine();

        // main no puede llamarse explícitamente
        if ($funcName === 'main') {
            $this->addSemanticError(
                "La función 'main' no puede ser invocada explícitamente",
                $line,
                $column
            );
            return Value::nil();
        }

        // Recolectar argumentos
        $args    = [];
        $argList = $context->argumentList();

        if ($argList) {
            for ($i = 0; $i < $argList->getChildCount(); $i += 2) {
                $argCtx  = $argList->getChild($i);
                $args[] = $this->visit($argCtx);
            }
        }

        // Ejecutar función
        if ($this->functionExists($funcName)) {
            $func = $this->getFunction($funcName);
            return $func(...$args);
        }

        $this->addSemanticError(
            "Función no definida: '$funcName'",
            $line,
            $column
        );

        return Value::nil();
    }
}