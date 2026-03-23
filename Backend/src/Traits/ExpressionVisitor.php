<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;

/**
 * Trait para visitar expresiones del AST.
 */
trait ExpressionVisitor
{
    // =========================================================
    //  LITERALES
    // =========================================================

    public function visitIntLiteral($context)
    {
        return Value::int32((int) $context->INT32()->getText());
    }

    public function visitFloatLiteral($context)
    {
        return Value::float32((float) $context->FLOAT32()->getText());
    }

    public function visitRuneLiteral($context)
    {
        $text = $context->RUNE()->getText();    // e.g. 'a'  o  '\n'
        $inner = substr($text, 1, -1);          // quitar comillas simples

        if (strlen($inner) === 1) {
            return Value::rune(ord($inner));
        }

        // Secuencias de escape
        $char = match ($inner) {
            '\\n'  => "\n",
            '\\t'  => "\t",
            '\\r'  => "\r",
            '\\\\'  => '\\',
            "\\'"  => "'",
            '\\0'  => "\0",
            default => $inner,
        };

        return Value::rune(ord($char[0]));
    }

    public function visitStringLiteral($context)
    {
        $text  = $context->STRING()->getText();
        $value = substr($text, 1, -1);
        $value = str_replace(['\\n','\\t','\\r','\\\\'], ["\n","\t","\r",'\\'], $value);
        return Value::string($value);
    }

    public function visitTrueLiteral($context)  { return Value::bool(true);  }
    public function visitFalseLiteral($context) { return Value::bool(false); }
    public function visitNilLiteral($context)   { return Value::nil();       }

    // =========================================================
    //  AGRUPACIÓN
    // =========================================================

    public function visitGroupedExpression($context)
    {
        return $this->visit($context->expression());
    }

    // =========================================================
    //  ARITMÉTICA
    // =========================================================

    public function visitAdditive($context)
    {
        $left  = $this->visit($context->multiplicative(0));
        $mIdx  = 1;

        for ($i = 1; $i < $context->getChildCount(); $i += 2) {
            $op    = $context->getChild($i)->getText();
            $right = $this->visit($context->multiplicative($mIdx++));
            $line  = $context->getStart()->getLine();
            $col   = $context->getStart()->getCharPositionInLine();

            $left = ($op === '+')
                ? $this->performAddition($left, $right, $line, $col)
                : $this->performSubtraction($left, $right, $line, $col);
        }

        return $left;
    }

    public function visitMultiplicative($context)
    {
        $left = $this->visit($context->unary(0));
        $uIdx = 1;

        for ($i = 1; $i < $context->getChildCount(); $i += 2) {
            $op    = $context->getChild($i)->getText();
            $right = $this->visit($context->unary($uIdx++));
            $line  = $context->getStart()->getLine();
            $col   = $context->getStart()->getCharPositionInLine();

            $left = match ($op) {
                '*' => $this->performMultiplication($left, $right, $line, $col),
                '/' => $this->performDivision($left, $right, $line, $col),
                '%' => $this->performModulo($left, $right, $line, $col),
            };
        }

        return $left;
    }

    // =========================================================
    //  UNARIOS
    // =========================================================

    public function visitPrimaryUnary($context)
    {
        return $this->visit($context->primary());
    }

    public function visitNegativeUnary($context)
    {
        $val = $this->visit($context->unary());

        if ($val->getType() === 'int32')   return Value::int32(-$val->getValue());
        if ($val->getType() === 'float32') return Value::float32(-$val->getValue());

        return Value::nil();
    }

    public function visitNotUnary($context)
    {
        $val = $this->visit($context->unary());
        return Value::bool(!$val->toBool());
    }

    /**
     * &ID → crea un puntero a la variable en el entorno actual.
     */
    public function visitAddressOf($context)
    {
        $varName = $context->ID()->getText();

        if (!$this->environment->exists($varName)) {
            $this->addSemanticError(
                "Variable '$varName' no declarada",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return Value::nil();
        }

        return Value::pointer($varName, $this->environment);
    }

    /**
     * *unary → desreferencia un puntero.
     */
    public function visitDereference($context)
    {
        $val = $this->visit($context->unary());

        if ($val->getType() !== 'pointer') {
            $this->addSemanticError(
                "No se puede desreferenciar un valor de tipo '{$val->getType()}'",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return Value::nil();
        }

        $data = $val->getValue();
        return $data['env']->get($data['varName']) ?? Value::nil();
    }

    // =========================================================
    //  COMPARACIONES
    // =========================================================

    public function visitEquality($context)
    {
        if ($context->getChildCount() === 1) {
            return $this->visit($context->relational(0));
        }

        $left = $this->visit($context->relational(0));
        $rIdx = 1;

        for ($i = 1; $i < $context->getChildCount(); $i += 2) {
            $op    = $context->getChild($i)->getText();
            $right = $this->visit($context->relational($rIdx++));
            $left  = $this->performComparison($op, $left, $right);
        }

        return $left;
    }

    public function visitRelational($context)
    {
        if ($context->getChildCount() === 1) {
            return $this->visit($context->additive(0));
        }

        $left = $this->visit($context->additive(0));
        $aIdx = 1;

        for ($i = 1; $i < $context->getChildCount(); $i += 2) {
            $op    = $context->getChild($i)->getText();
            $right = $this->visit($context->additive($aIdx++));
            $left  = $this->performComparison($op, $left, $right);
        }

        return $left;
    }

    // =========================================================
    //  LÓGICOS (con cortocircuito)
    // =========================================================

    public function visitLogicalAnd($context)
    {
        if ($context->getChildCount() === 1) {
            return $this->visit($context->equality(0));
        }

        $left = $this->visit($context->equality(0));
        $eIdx = 1;

        for ($i = 1; $i < $context->getChildCount(); $i += 2) {
            if (!$left->toBool()) return Value::bool(false); // cortocircuito
            $right = $this->visit($context->equality($eIdx++));
            $left  = Value::bool($right->toBool());
        }

        return Value::bool($left->toBool());
    }

    public function visitLogicalOr($context)
    {
        if ($context->getChildCount() === 1) {
            return $this->visit($context->logicalAnd(0));
        }

        $left = $this->visit($context->logicalAnd(0));
        $aIdx = 1;

        for ($i = 1; $i < $context->getChildCount(); $i += 2) {
            if ($left->toBool()) return Value::bool(true); // cortocircuito
            $right = $this->visit($context->logicalAnd($aIdx++));
            $left  = Value::bool($right->toBool());
        }

        return Value::bool($left->toBool());
    }

    // =========================================================
    //  ARGUMENTOS DE FUNCIÓN
    // =========================================================

    /**
     * Argumento normal: una expresión.
     */
    public function visitExpressionArgument($context)
    {
        return $this->visit($context->expression());
    }

    /**
     * Argumento por referencia: &ID → puntero.
     */
    public function visitAddressArgument($context)
    {
        $varName = $context->ID()->getText();

        if (!$this->environment->exists($varName)) {
            $this->addSemanticError(
                "Variable '$varName' no declarada",
                $context->getStart()->getLine(),
                $context->getStart()->getCharPositionInLine()
            );
            return Value::nil();
        }

        return Value::pointer($varName, $this->environment);
    }
}