<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;

/**
 * Trait para visitar declaraciones del AST.
 * Actualizado para soportar arreglos, punteros y tipos primitivos.
 */
trait DeclarationVisitor
{
    // =========================================================
    //  VAR CON INICIALIZACIÓN
    // =========================================================

    public function visitVarDeclWithInit($context)
    {
        $idList         = $context->idList();
        $typeCtx        = $context->type();
        $expressionList = $context->expressionList();

        $isArray   = $this->isArrayTypeCtx($typeCtx);
        $isPointer = $this->isPointerTypeCtx($typeCtx);
        $type      = $isArray   ? 'array'
                   : ($isPointer ? 'pointer'
                   : $this->extractType($typeCtx));

        $ids = [];
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $ids[] = $idList->getChild($i)->getText();
        }

        $expressions = [];
        for ($i = 0; $i < $expressionList->getChildCount(); $i += 2) {
            $expressions[] = $this->visit($expressionList->getChild($i));
        }

        if (count($ids) !== count($expressions)) {
            $this->addSemanticError(
                "Número de variables (" . count($ids) . ") no coincide con número de valores (" . count($expressions) . ")",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return null;
        }

        for ($i = 0; $i < count($ids); $i++) {
            $varName = $ids[$i];
            $value   = $expressions[$i];

            if ($this->symbolExistsInCurrentScope($varName)) {
                $this->addSemanticError(
                    "Variable '$varName' ya ha sido declarada en el ámbito actual",
                    $context->getStart()->getLine(),
                    $context->getStart()->getCharPositionInLine()
                );
                continue;
            }

            // Verificar compatibilidad de tipo
            if (!$value->isNil()) {
                if ($isPointer) {
                    // Para punteros: el valor asignado debe ser un puntero
                    if ($value->getType() !== 'pointer') {
                        $this->addSemanticError(
                            "Incompatibilidad de tipos: se esperaba un puntero (*T) pero se obtuvo '{$value->getType()}'",
                            $context->getStart()->getLine(),
                            $context->getStart()->getCharPositionInLine()
                        );
                    }
                } elseif ($isArray && $value->getType() !== 'array') {
                    $this->addSemanticError(
                        "Incompatibilidad de tipos: se esperaba un arreglo pero se obtuvo '{$value->getType()}'",
                        $context->getStart()->getLine(),
                        $context->getStart()->getCharPositionInLine()
                    );
                } elseif (!$isArray && !$isPointer && $value->getType() !== $type) {
                    $this->addSemanticError(
                        "Incompatibilidad de tipos: se esperaba '$type' pero se obtuvo '{$value->getType()}'",
                        $context->getStart()->getLine(),
                        $context->getStart()->getCharPositionInLine()
                    );
                }
            }

            $this->environment->define($varName, $value);

            $this->addSymbol(
                $varName,
                $this->buildTypeLabel($typeCtx),
                $this->getCurrentScopeName(),
                $value,
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
        }

        return null;
    }

    // =========================================================
    //  VAR SIN INICIALIZACIÓN
    // =========================================================

    public function visitVarDeclSimple($context)
    {
        $idList  = $context->idList();
        $typeCtx = $context->type();

        $isArray   = $this->isArrayTypeCtx($typeCtx);
        $isPointer = $this->isPointerTypeCtx($typeCtx);
        $type      = $isArray   ? 'array'
                   : ($isPointer ? 'pointer'
                   : $this->extractType($typeCtx));

        $ids = [];
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $ids[] = $idList->getChild($i)->getText();
        }

        foreach ($ids as $id) {
            if ($this->symbolExistsInCurrentScope($id)) {
                $this->addSemanticError(
                    "Variable '$id' ya ha sido declarada en el ámbito actual",
                    $context->getStart()->getLine(),
                    $context->getStart()->getCharPositionInLine()
                );
                continue;
            }

            // Punteros sin inicialización → nil (equivalente a null en Go)
            $defaultValue = $isArray   ? $this->createArrayFromTypeCtx($typeCtx)
                          : ($isPointer ? Value::nil()
                          : $this->getDefaultValue($type));

            $this->environment->define($id, $defaultValue);

            $this->addSymbol(
                $id,
                $this->buildTypeLabel($typeCtx),
                $this->getCurrentScopeName(),
                $defaultValue,
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
        }

        return null;
    }

    // =========================================================
    //  CONSTANTES
    // =========================================================

    public function visitConstDecl($context)
    {
        $id      = $context->ID()->getText();
        $typeCtx = $context->type();
        $type    = $this->extractType($typeCtx);
        $value   = $this->visit($context->expression());

        if ($this->symbolExistsInCurrentScope($id)) {
            $this->addSemanticError(
                "Constante '$id' ya ha sido declarada en el ámbito actual",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return null;
        }

        if (!$value->isNil() && $value->getType() !== $type) {
            $this->addSemanticError(
                "Incompatibilidad de tipos en constante '$id': se esperaba '$type' pero se obtuvo '{$value->getType()}'",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
        }

        $this->environment->define($id, $value);

        $this->addSymbol(
            $id,
            $type . ' (const)',
            $this->getCurrentScopeName(),
            $value,
            $context->getStart()->getLine(),
            $context->getStart()->getCharPositionInLine()
        );

        return null;
    }

    // =========================================================
    //  IDENTIFICADOR (LECTURA)
    // =========================================================

    public function visitIdentifier($context)
    {
        $varName = $context->ID()->getText();
        $value   = $this->environment->get($varName);

        if ($value === null) {
            $this->addSemanticError(
                "Variable '$varName' no declarada",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return Value::nil();
        }

        // Auto-desreferenciar punteros en contexto de expresión.
        // visitArrayAccess / visitArrayAssignment leen el env directamente
        // y manejan punteros con su propio FIX 3, así que esto no interfiere.
        if ($value->getType() === 'pointer') {
            $data  = $value->getValue();
            $deref = $data['env']->get($data['varName']);
            if ($deref !== null) {
                return $deref;
            }
        }

        return $value;
    }

    // =========================================================
    //  HELPERS DE TIPO
    // =========================================================

    /**
     * Extrae el tipo base como string.
     * Para arreglos devuelve 'array', para punteros devuelve 'pointer'.
     */
    protected function extractType($typeCtx): string
    {
        if ($typeCtx === null) {
            return 'nil';
        }

        if ($this->isArrayTypeCtx($typeCtx)) {
            return 'array';
        }

        if ($this->isPointerTypeCtx($typeCtx)) {
            return 'pointer';
        }

        $text = $typeCtx->getText();

        return match ($text) {
            'int32'   => 'int32',
            'float32' => 'float32',
            'bool'    => 'bool',
            'string'  => 'string',
            'rune'    => 'rune',
            default   => 'nil',
        };
    }

    /**
     * Detecta si un contexto de tipo es un puntero (*T).
     */
    protected function isPointerTypeCtx($typeCtx): bool
    {
        if ($typeCtx === null) {
            return false;
        }
        return str_starts_with(trim($typeCtx->getText()), '*');
    }

    /**
     * Construye la etiqueta legible del tipo para la tabla de símbolos.
     * Ejemplos: 'int32', '[5]int32', '[2][3]int32', '*int32'
     */
    protected function buildTypeLabel($typeCtx): string
    {
        if ($typeCtx === null) {
            return 'nil';
        }
        return $typeCtx->getText();
    }

    /**
     * Devuelve el valor por defecto para un tipo primitivo.
     */
    protected function getDefaultValue(string $type): Value
    {
        return match ($type) {
            'int32'   => Value::int32(0),
            'float32' => Value::float32(0.0),
            'bool'    => Value::bool(false),
            'string'  => Value::string(''),
            'rune'    => Value::rune(0),
            default   => Value::nil(),
        };
    }
}