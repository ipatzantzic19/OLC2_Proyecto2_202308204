<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;

/**
 * Trait para manejar incremento (++) y decremento (--)
 */
trait IncrementDecrementVisitor
{
    /**
     * Visita una sentencia de incremento: i++
     */
    public function visitIncrementStatement($context)
    {
        $varName = $context->ID()->getText();
        $line = $context->getStart()->getLine();
        $column = $context->getStart()->getCharPositionInLine();
        
        // Verificar que la variable existe
        if (!$this->environment->exists($varName)) {
            $this->addSemanticError(
                "Variable '$varName' no declarada",
                $line,
                $column
            );
            return null;
        }
        
        // Obtener valor actual
        $current = $this->environment->get($varName);
        
        if ($current === null) {
            $this->addSemanticError(
                "Variable '$varName' no encontrada",
                $line,
                $column
            );
            return null;
        }
        
        // Solo funciona con int32 y rune
        if ($current->getType() !== 'int32' && $current->getType() !== 'rune') {
            $this->addSemanticError(
                "Operador '++' solo es válido para tipos int32 y rune, se intentó usar con '{$current->getType()}'",
                $line,
                $column
            );
            return null;
        }
        
        // Incrementar: i = i + 1
        if ($current->getType() === 'int32') {
            $newValue = Value::int32($current->getValue() + 1);
        } else { // rune
            $newValue = Value::rune($current->getValue() + 1);
        }
        
        // ACTUALIZR en el entorno
        $this->environment->set($varName, $newValue);
        
        // ACTUALIZAR en la tabla de símbolos
        $this->updateSymbolValue($varName, $newValue);
        
        return null;
    }
    
    /**
     * Visita una sentencia de decremento: i--
     */
    public function visitDecrementStatement($context)
    {
        $varName = $context->ID()->getText();
        $line = $context->getStart()->getLine();
        $column = $context->getStart()->getCharPositionInLine();
        
        // Verificar que la variable exists
        if (!$this->environment->exists($varName)) {
            $this->addSemanticError(
                "Variable '$varName' no declarada",
                $line,
                $column
            );
            return null;
        }
        
        // Obtener valor actual
        $current = $this->environment->get($varName);
        
        if ($current === null) {
            $this->addSemanticError(
                "Variable '$varName' no encontrada",
                $line,
                $column
            );
            return null;
        }
        
        // Solo funciona con int32 y rune
        if ($current->getType() !== 'int32' && $current->getType() !== 'rune') {
            $this->addSemanticError(
                "Operador '--' solo es válido para tipos int32 y rune, se intentó usar con '{$current->getType()}'",
                $line,
                $column
            );
            return null;
        }
        
        // Decrementar: i = i - 1
        if ($current->getType() === 'int32') {
            $newValue = Value::int32($current->getValue() - 1);
        } else { // rune
            $newValue = Value::rune($current->getValue() - 1);
        }
        
        $this->environment->set($varName, $newValue);
        
        $this->updateSymbolValue($varName, $newValue);
        
        return null;
    }
}