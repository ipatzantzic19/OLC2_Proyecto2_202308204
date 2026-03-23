<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;

/**
 * Trait para manejar asignaciones en el AST.
 *
 * FASE 6: Validación de inmutabilidad de constantes en:
 *   - visitSimpleAssignment  → bloquea `const = expr`
 *   - visitShortVarDecl      → bloquea `const := expr` (reasignación)
 *   - visitPointerAssignment → sin cambio (puntero no puede apuntar a const
 *                              porque &const no es válido en Go / Golampi)
 */
trait AssignmentVisitor
{
    // =========================================================
    //  ASIGNACIÓN SIMPLE (x = expr, x += expr, …)
    // =========================================================

    public function visitSimpleAssignment($context)
    {
        $varName   = $context->ID()->getText();
        $assignOp  = $context->assignOp()->getText();
        $line      = $context->getStart()->getLine();
        $column    = $context->getStart()->getCharPositionInLine();

        // ── FASE 6: guard de inmutabilidad ──────────────────────────
        if ($this->isConstant($varName)) {
            $this->addSemanticError(
                "No se puede modificar la constante '$varName'",
                $line,
                $column
            );
            return null;
        }
        // ────────────────────────────────────────────────────────────

        $exprValue = $this->visit($context->expression());

        if (!$this->environment->exists($varName)) {
            $this->addSemanticError("Variable '$varName' no declarada", $line, $column);
            return null;
        }

        $currentValue = $this->environment->get($varName);
        if ($currentValue === null) {
            $this->addSemanticError("Variable '$varName' no encontrada", $line, $column);
            return null;
        }

        // Si la variable es un puntero, escribir a través de él (semántica por referencia)
        $isPointerVar  = ($currentValue->getType() === 'pointer');
        $writeEnv      = $this->environment;
        $writeVarName  = $varName;
        $effectiveCurr = $currentValue;
        if ($isPointerVar) {
            $ptrData       = $currentValue->getValue();
            $writeVarName  = $ptrData['varName'];
            $writeEnv      = $ptrData['env'];
            $effectiveCurr = $writeEnv->get($writeVarName) ?? $currentValue;
        }

        $newValue = match ($assignOp) {
            '='  => $exprValue,
            '+=' => $this->performAddition($effectiveCurr, $exprValue, $line, $column),
            '-=' => $this->performSubtraction($effectiveCurr, $exprValue, $line, $column),
            '*=' => $this->performMultiplication($effectiveCurr, $exprValue, $line, $column),
            '/=' => $this->performDivision($effectiveCurr, $exprValue, $line, $column),
            default => null,
        };

        if ($newValue === null) {
            $this->addSemanticError(
                "Operador de asignación desconocido: '$assignOp'", $line, $column
            );
            return null;
        }

        // Verificar compatibilidad de tipos solo en asignación simple '='
        if ($assignOp === '=' && !$newValue->isNil()) {
            $currentType = $effectiveCurr->getType();
            $newType     = $newValue->getType();

            if ($currentType !== $newType
                && !($currentType === 'array' && $newType === 'array')
            ) {
                $this->addSemanticError(
                    "Incompatibilidad de tipos: no se puede asignar '$newType'"
                    . " a variable de tipo '$currentType'",
                    $line, $column
                );
                return null;
            }
        }

        // Escribir en el entorno correcto (a través del puntero si aplica)
        $writeEnv->set($writeVarName, $newValue);
        $this->updateSymbolValue($writeVarName, $newValue);

        return null;
    }

    // =========================================================
    //  ASIGNACIÓN A PUNTERO (*ptr = expr, *ptr += expr, …)
    // =========================================================

    public function visitPointerAssignment($context)
    {
        $ptrName  = $context->ID()->getText();
        $assignOp = $context->assignOp()->getText();
        $newValue = $this->visit($context->expression());

        $line   = $context->getStart()->getLine();
        $column = $context->getStart()->getCharPositionInLine();

        $ptrValue = $this->environment->get($ptrName);

        if ($ptrValue === null) {
            $this->addSemanticError(
                "Variable '$ptrName' no declarada", $line, $column
            );
            return null;
        }

        if ($ptrValue->getType() !== 'pointer') {
            $this->addSemanticError(
                "'$ptrName' no es un puntero (tipo: '{$ptrValue->getType()}')",
                $line, $column
            );
            return null;
        }

        $data    = $ptrValue->getValue();
        $varName = $data['varName'];
        $env     = $data['env'];

        $current = $env->get($varName);

        if ($current === null) {
            $this->addSemanticError(
                "El puntero '$ptrName' apunta a una variable no válida",
                $line, $column
            );
            return null;
        }

        $finalValue = match ($assignOp) {
            '='  => $newValue,
            '+=' => $this->performAddition($current, $newValue, $line, $column),
            '-=' => $this->performSubtraction($current, $newValue, $line, $column),
            '*=' => $this->performMultiplication($current, $newValue, $line, $column),
            '/=' => $this->performDivision($current, $newValue, $line, $column),
            default => null,
        };

        if ($finalValue === null) {
            $this->addSemanticError(
                "Operador de asignación desconocido: '$assignOp'", $line, $column
            );
            return null;
        }

        $env->set($varName, $finalValue);
        $this->updateSymbolValue($varName, $finalValue);

        return null;
    }

    // =========================================================
    //  DECLARACIÓN CORTA (x := expr  o  x, y := f())
    // =========================================================

    public function visitShortVarDecl($context)
    {
        $idList         = $context->idList();
        $expressionList = $context->expressionList();

        $line   = $context->getStart()->getLine();
        $column = $context->getStart()->getCharPositionInLine();

        $ids = [];
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $ids[] = $idList->getChild($i)->getText();
        }

        $expressions = [];
        for ($i = 0; $i < $expressionList->getChildCount(); $i += 2) {
            $expressions[] = $this->visit($expressionList->getChild($i));
        }

        // Desempacar retorno múltiple
        if (count($ids) > 1
            && count($expressions) === 1
            && $expressions[0] instanceof Value
            && $expressions[0]->getType() === 'multi'
        ) {
            $expressions = $expressions[0]->getValue();
        }

        if (count($ids) !== count($expressions)) {
            $this->addSemanticError(
                "Número de variables (" . count($ids) . ") no coincide"
                . " con número de valores (" . count($expressions) . ")",
                $line, $column
            );
            return null;
        }

        $scopeName = $this->getCurrentScopeName();
        if ($scopeName === 'global') {
            $this->addSemanticError(
                "La declaración corta (:=) no puede usarse a nivel global",
                $line, $column
            );
            return null;
        }

        // ── FASE 6: ninguna id puede ser una constante ───────────────
        foreach ($ids as $id) {
            if ($this->isConstant($id)) {
                $this->addSemanticError(
                    "No se puede reasignar la constante '$id' mediante ':='",
                    $line, $column
                );
                return null;
            }
        }
        // ────────────────────────────────────────────────────────────

        $atLeastOneNew = false;
        foreach ($ids as $id) {
            if (!$this->environment->exists($id)) {
                $atLeastOneNew = true;
                break;
            }
        }

        if (!$atLeastOneNew) {
            $this->addSemanticError(
                "Declaración corta requiere que al menos una variable sea nueva",
                $line, $column
            );
            return null;
        }

        for ($i = 0; $i < count($ids); $i++) {
            $varName = $ids[$i];
            $value   = $expressions[$i];

            if ($value->isNil()) {
                $this->addSemanticError(
                    "No se puede inferir el tipo de una expresión nil en declaración corta",
                    $line, $column
                );
                continue;
            }

            $inferredType = $value->getType();

            if ($this->environment->exists($varName)) {
                $currentValue = $this->environment->get($varName);

                if ($currentValue->getType() !== $inferredType) {
                    $this->addSemanticError(
                        "Incompatibilidad de tipos: '$varName' es '{$currentValue->getType()}'"
                        . " pero se asigna '$inferredType'",
                        $line, $column
                    );
                    continue;
                }

                $this->environment->set($varName, $value);
                $this->updateSymbolValue($varName, $value);

            } else {
                $this->environment->define($varName, $value);

                $added = $this->addSymbol(
                    $varName, $inferredType, $scopeName,
                    $value, $line, $column
                );

                if (!$added) {
                    $this->updateSymbolValue($varName, $value);
                }
            }
        }

        return null;
    }
}