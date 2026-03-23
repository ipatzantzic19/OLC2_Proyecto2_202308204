<?php

namespace Golampi\Visitor;

use Golampi\Runtime\Value;
use Golampi\Runtime\Environment;
use Golampi\Traits\ErrorHandler;
use Golampi\Traits\SymbolTableManager;
use Golampi\Traits\BuiltinFunctionsVisitor;

/**
 * Clase base del visitor Golampi.
 * Inicializa funciones embebidas y provee utilidades comunes.
 */
require_once __DIR__ . '/../../generated/GolampiVisitor.php';
require_once __DIR__ . '/../../generated/GolampiBaseVisitor.php';

abstract class BaseVisitor extends \GolampiBaseVisitor
{
    use ErrorHandler;
    use SymbolTableManager;
    use BuiltinFunctionsVisitor; 

    protected Environment $environment;
    protected array       $output    = [];
    protected array       $functions = [];

    // =========================================================
    //  FUNCIONES EMBEBIDAS
    // =========================================================
    public function __construct()
    {
        $this->environment = new Environment();
        $this->initBuiltinFunctions();
    }

    // =========================================================
    //  CONVERSIÓN A STRING PARA SALIDA
    // =========================================================

    /**
     * Convierte cualquier Value en su representación de cadena para la consola.
     * Maneja arreglos de forma recursiva.
     */
    protected function valueToOutputString(Value $val): string
    {
        if ($val->getType() === 'array') {
            // Delegar al trait ArrayVisitor si está disponible
            if (method_exists($this, 'arrayToString')) {
                return $this->arrayToString($val, false);
            }
            // Fallback simple
            $data  = $val->getValue();
            $parts = [];
            foreach ($data['elements'] as $el) {
                $parts[] = $this->valueToOutputString($el);
            }
            return '[' . implode(' ', $parts) . ']';
        }

        return $val->toString();
    }

    // =========================================================
    //  GESTIÓN DE FUNCIONES
    // =========================================================

    protected function defineFunction(string $name, callable $func): void
    {
        $this->functions[$name] = $func;
    }

    protected function getFunction(string $name): ?callable
    {
        return $this->functions[$name] ?? null;
    }

    protected function functionExists(string $name): bool
    {
        return isset($this->functions[$name]);
    }

    // =========================================================
    //  SALIDA
    // =========================================================

    public function getOutput(): array
    {
        return $this->output;
    }

    public function clearOutput(): void
    {
        $this->output = [];
    }

    public function getOutputString(): string
    {
        return implode("\n", $this->output);
    }
}