<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;
use Golampi\Runtime\Environment;
use Golampi\Exceptions\BreakException;
use Golampi\Exceptions\ContinueException;
use Golampi\Exceptions\ReturnException;

/**
 * Trait para visitar sentencias de control de flujo del AST.
 */
trait ControlFlowVisitor
{
    // =========================================================
    //  IF / ELSE-IF / ELSE
    // =========================================================

    public function visitIfElseIfElse($context)
    {
        $conditions = $context->expression();

        foreach ($conditions as $index => $conditionCtx) {
            $conditionResult = $this->visit($conditionCtx);

            if (!$conditionResult instanceof Value) {
                $this->addSemanticError(
                    "La condición del 'if' debe ser una expresión válida",
                    $conditionCtx->getStart()->getLine(),
                    $conditionCtx->getStart()->getCharPositionInLine()
                );
                return null;
            }

            if ($conditionResult->toBool()) {
                $parentEnv = $this->environment;
                $this->environment = new Environment($parentEnv);
                $this->enterScope('if-block');

                try {
                    $this->visit($context->block($index));
                } finally {
                    $this->exitScope();
                    $this->environment = $parentEnv;
                }

                return null;
            }
        }

        // Bloque else (si existe)
        if (count($context->block()) > count($conditions)) {
            $elseBlock = $context->block(count($context->block()) - 1);

            $parentEnv = $this->environment;
            $this->environment = new Environment($parentEnv);
            $this->enterScope('else-block');

            try {
                $this->visit($elseBlock);
            } finally {
                $this->exitScope();
                $this->environment = $parentEnv;
            }
        }

        return null;
    }

    // =========================================================
    //  FOR TRADICIONAL
    // =========================================================

    public function visitForTraditional($context)
    {
        $parentEnv = $this->environment;
        $this->environment = new Environment($parentEnv);
        $this->enterScope('for');

        try {
            $forClause = $context->forClause();

            // 1. Inicialización
            $initCtx = $forClause->forInit();
            if ($initCtx->varDeclaration())     { $this->visit($initCtx->varDeclaration()); }
            elseif ($initCtx->shortVarDeclaration()) { $this->visit($initCtx->shortVarDeclaration()); }
            elseif ($initCtx->assignment())     { $this->visit($initCtx->assignment()); }
            elseif ($initCtx->incDecStatement()){ $this->visit($initCtx->incDecStatement()); }

            // 2. Loop
            while (true) {
                if ($forClause->expression()) {
                    $condition = $this->visit($forClause->expression());
                    if (!$condition instanceof Value || !$condition->toBool()) {
                        break;
                    }
                }

                try {
                    $this->visit($context->block());
                } catch (BreakException $e) {
                    break;
                } catch (ContinueException $e) {
                    // continúa al post-incremento
                }

                // 3. Post-incremento
                $postCtx = $forClause->forPost();
                if ($postCtx->assignment())     { $this->visit($postCtx->assignment()); }
                elseif ($postCtx->incDecStatement()) { $this->visit($postCtx->incDecStatement()); }
            }

        } catch (ReturnException $e) {
            throw $e;
        } finally {
            $this->exitScope();
            $this->environment = $parentEnv;
        }

        return null;
    }

    // =========================================================
    //  FOR WHILE
    // =========================================================

    public function visitForWhile($context)
    {
        $parentEnv = $this->environment;
        $this->environment = new Environment($parentEnv);
        $this->enterScope('for');

        try {
            while (true) {
                $condition = $this->visit($context->expression());
                if (!$condition instanceof Value || !$condition->toBool()) {
                    break;
                }

                try {
                    $this->visit($context->block());
                } catch (BreakException $e) {
                    break;
                } catch (ContinueException $e) {
                    continue;
                }
            }
        } catch (ReturnException $e) {
            throw $e;
        } finally {
            $this->exitScope();
            $this->environment = $parentEnv;
        }

        return null;
    }

    // =========================================================
    //  FOR INFINITO
    // =========================================================

    public function visitForInfinite($context)
    {
        $parentEnv = $this->environment;
        $this->environment = new Environment($parentEnv);
        $this->enterScope('for');

        try {
            while (true) {
                try {
                    $this->visit($context->block());
                } catch (BreakException $e) {
                    break;
                } catch (ContinueException $e) {
                    continue;
                }
            }
        } catch (ReturnException $e) {
            throw $e;
        } finally {
            $this->exitScope();
            $this->environment = $parentEnv;
        }

        return null;
    }

    // =========================================================
    //  SWITCH
    // =========================================================

    public function visitSwitchStatement($context)
    {
        $parentEnv = $this->environment;
        $this->environment = new Environment($parentEnv);
        $this->enterScope('switch');

        try {
            $switchValue = $this->visit($context->expression());

            $caseClauses   = [];
            $defaultClause = null;

            for ($i = 0; $i < $context->getChildCount(); $i++) {
                $child = $context->getChild($i);
                if ($child instanceof \Antlr\Antlr4\Runtime\Tree\TerminalNode) continue;

                if (method_exists($child, 'expressionList')) {
                    $caseClauses[] = $child;
                } elseif (method_exists($child, 'statement') && !method_exists($child, 'expressionList')) {
                    $defaultClause = $child;
                }
            }

            $matched       = false;
            $shouldExecute = false;

            foreach ($caseClauses as $caseClause) {
                $expressionList = $caseClause->expressionList();

                for ($i = 0; $i < $expressionList->getChildCount(); $i += 2) {
                    $caseValue = $this->visit($expressionList->getChild($i));
                    if (!$matched && $this->valuesEqual($switchValue, $caseValue)) {
                        $matched       = true;
                        $shouldExecute = true;
                        break;
                    }
                }

                if ($shouldExecute) {
                    for ($i = 0; $i < $caseClause->getChildCount(); $i++) {
                        $child = $caseClause->getChild($i);
                        if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                            $this->visit($child);
                        }
                    }
                    $shouldExecute = false; // En Go el switch no tiene fallthrough por defecto
                    break;
                }
            }

            if (!$matched && $defaultClause !== null) {
                for ($i = 0; $i < $defaultClause->getChildCount(); $i++) {
                    $child = $defaultClause->getChild($i);
                    if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                        $this->visit($child);
                    }
                }
            }

        } catch (BreakException $e) {
            // break sale del switch
        } catch (ReturnException $e) {
            throw $e;
        } finally {
            $this->exitScope();
            $this->environment = $parentEnv;
        }

        return null;
    }

    // =========================================================
    //  BREAK / CONTINUE / RETURN
    // =========================================================

    public function visitBreakStatement($context)
    {
        if (!$this->isInsideScope(['for', 'switch'])) {
            $this->addSemanticError(
                "La sentencia 'break' solo puede usarse dentro de un ciclo ('for') o un 'switch'",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return null;
        }
        throw new BreakException();
    }

    public function visitContinueStatement($context)
    {
        if (!$this->isInsideScope(['for'])) {
            $this->addSemanticError(
                "La sentencia 'continue' solo puede usarse dentro de un ciclo ('for')",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return null;
        }
        throw new ContinueException();
    }

    /**
     * Visita un return. Soporta retorno vacío, simple y múltiple.
     */
    public function visitReturnStatement($context)
    {
        if (!$this->isInsideScope(['function'])) {
            $this->addSemanticError(
                "La sentencia 'return' solo puede usarse dentro de una función",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return null;
        }

        $expressionList = $context->expressionList();

        if ($expressionList === null) {
            throw new ReturnException(Value::nil());
        }

        $returnValues = [];
        for ($i = 0; $i < $expressionList->getChildCount(); $i += 2) {
            $returnValues[] = $this->visit($expressionList->getChild($i));
        }

        if (count($returnValues) === 0) {
            throw new ReturnException(Value::nil());
        }

        if (count($returnValues) === 1) {
            throw new ReturnException($returnValues[0]);
        }

        // Múltiples valores de retorno → Value::multi
        throw new ReturnException(Value::multi($returnValues));
    }

    // =========================================================
    //  HELPERS PRIVADOS
    // =========================================================

    /**
     * Compara dos valores para igualdad (usado en switch).
     */
    private function valuesEqual(Value $a, Value $b): bool
    {
        if ($a->getType() !== $b->getType()) return false;
        return $a->getValue() == $b->getValue();
    }

    /**
     * Verifica si el visitante está dentro de un scope del tipo indicado.
     * Soporta coincidencia exacta Y de prefijo (p.ej. 'function' coincide
     * con 'function:main', 'function:suma', etc.).
     */
    private function isInsideScope(array $scopeTypes): bool
    {
        for ($i = count($this->scopeStack) - 1; $i >= 0; $i--) {
            $scopeName = $this->scopeStack[$i]['name'];
            foreach ($scopeTypes as $type) {
                if ($scopeName === $type
                    || str_starts_with($scopeName, $type . ':')
                ) {
                    return true;
                }
            }
        }
        return false;
    }
}