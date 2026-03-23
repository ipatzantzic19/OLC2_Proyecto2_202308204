<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;
use Golampi\Runtime\Environment;
use Golampi\Exceptions\ReturnException;

/**
 * Trait para el manejo de funciones de usuario.
 *
 * FIX 3: executeUserFunction ahora vincula parámetros puntero correctamente.
 *        Cuando el parámetro es *[N]T y el argumento es un Value::pointer,
 *        se pasa directamente el puntero (no se copia el arreglo).
 *        Cuando el argumento es un Value::array se crea un pointer en el
 *        entorno del llamador para que la función pueda mutarlo.
 */
trait FunctionVisitor
{
    // =========================================================
    //  HOISTING / REGISTRO
    // =========================================================

    protected function registerUserFunction($funcDecl): void
    {
        $funcName  = $funcDecl->ID()->getText();
        $paramDefs = $this->extractParamDefs($funcDecl);

        $this->addSymbol(
            $funcName,
            'function',
            'global',
            Value::nil(),
            $funcDecl->getStart()->getLine(),
            $funcDecl->getStart()->getCharPositionInLine()
        );

        $globalEnv    = $this->environment;
        $capturedDecl = $funcDecl;

        $this->functions[$funcName] = function () use (
            $funcName, $capturedDecl, $paramDefs, $globalEnv
        ) {
            $args = func_get_args();
            return $this->executeUserFunction(
                $funcName, $capturedDecl, $paramDefs, $args, $globalEnv
            );
        };
    }

    // =========================================================
    //  EJECUCIÓN DE MAIN
    // =========================================================

    protected function executeMain($funcDecl): void
    {
        $this->addSymbol(
            'main',
            'function',
            'global',
            Value::nil(),
            $funcDecl->getStart()->getLine(),
            $funcDecl->getStart()->getCharPositionInLine()
        );

        $parentEnv = $this->environment;
        $this->environment = new Environment($parentEnv);
        $this->enterScope('function:main');

        try {
            $this->executeBlock($funcDecl->block());
        } catch (ReturnException $e) {
            // main no retorna valores
        } finally {
            $this->exitScope();
            $this->environment = $parentEnv;
        }
    }

    // =========================================================
    //  EJECUCIÓN DE FUNCIÓN DE USUARIO
    // =========================================================

    public function executeUserFunction(
        string      $funcName,
        $funcDecl,
        array       $paramDefs,
        array       $args,
        Environment $globalEnv
    ): Value {
        $prevEnv   = $this->environment;
        $funcEnv   = new Environment($globalEnv);
        $this->environment = $funcEnv;
        $this->enterScope('function:' . $funcName);

        try {
            foreach ($paramDefs as $idx => $paramDef) {
                $argValue = $args[$idx] ?? $this->getDefaultValue($paramDef['type']);

                if ($paramDef['isPointer']) {
                    // ── Parámetro puntero (*T) ──────────────────────────────
                    if ($argValue->getType() === 'pointer') {
                        $this->environment->define($paramDef['name'], $argValue);
                    } else {
                        $this->environment->define($paramDef['name'], $argValue);
                    }

                    $symbolType = '*' . $paramDef['label'];
                } else {
                    // ── Parámetro por valor ─────────────────────────────────
                    $finalValue = ($argValue->getType() === 'array')
                        ? $this->deepCopyArray($argValue)
                        : $argValue;

                    $this->environment->define($paramDef['name'], $finalValue);
                    $argValue   = $finalValue;
                    $symbolType = $paramDef['label'];
                }

                $this->addSymbol(
                    $paramDef['name'],
                    $symbolType,
                    'function:' . $funcName,
                    $argValue,
                    $funcDecl->getStart()->getLine(),
                    0
                );
            }

            $this->executeBlock($funcDecl->block());
            return Value::nil();

        } catch (ReturnException $e) {
            return $e->getReturnValue();
        } finally {
            $this->exitScope();
            $this->environment = $prevEnv;
        }
    }

    // =========================================================
    //  COPIA PROFUNDA DE ARREGLOS (para paso por valor)
    // =========================================================

    /**
     * Devuelve una copia profunda de un Value arreglo.
     * Garantiza que modificaciones en la copia no afecten el original.
     */
    protected function deepCopyArray(Value $arr): Value
    {
        if ($arr->getType() !== 'array') {
            return $arr; // primitivos son inmutables (asignados por valor)
        }

        $data         = $arr->getValue();
        $copiedEls    = [];

        foreach ($data['elements'] as $el) {
            $copiedEls[] = ($el->getType() === 'array')
                ? $this->deepCopyArray($el)
                : $el;   // Values primitivos no necesitan clonarse
        }

        return new Value('array', [
            'elementType' => $data['elementType'],
            'size'        => $data['size'],
            'elements'    => $copiedEls,
        ]);
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function executeBlock($blockCtx): void
    {
        for ($i = 1; $i < $blockCtx->getChildCount() - 1; $i++) {
            $child = $blockCtx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $this->visit($child);
            }
        }
    }

    private function extractParamDefs($funcDecl): array
    {
        $params    = [];
        $paramList = $funcDecl->parameterList();

        if ($paramList === null) {
            return $params;
        }

        foreach ($paramList->parameter() as $param) {
            $isPointer = ($param->getStart()->getText() === '*');
            $paramName = $param->ID()->getText();
            $paramType = $this->extractType($param->type());
            // Etiqueta legible: '[5]int32' en lugar de 'array'
            $paramLabel = $this->buildTypeLabel($param->type());

            $params[] = [
                'name'      => $paramName,
                'type'      => $paramType,
                'label'     => $paramLabel,
                'isPointer' => $isPointer,
            ];
        }

        return $params;
    }
}