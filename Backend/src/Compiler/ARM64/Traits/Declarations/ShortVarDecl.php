<?php

namespace Golampi\Compiler\ARM64\Traits\Declarations;

/**
 * ShortVarDeclTrait — Generación ARM64 para declaración corta de variables
 *
 * Soporta la sintaxis:  x := expr   y   x, y := expr1, expr2
 *
 * La declaración corta es la forma idiomática en Golampi/Go de declarar
 * una variable infiriendo su tipo desde la expresión del lado derecho.
 *
 * Diferencias respecto a `var x T = expr`:
 *   - No se especifica el tipo explícitamente → se infiere del resultado
 *   - Solo es válida dentro de bloques (no a nivel global)
 *   - Al menos una variable debe ser nueva en el scope actual
 *
 * El tipo inferido lo devuelve el visitor de la expresión (string PHP:
 * 'int32', 'float32', 'bool', 'string', 'rune', etc.).
 *
 * Proceso de generación:
 *   1. Evaluar expr → resultado en x0 (int) o s0 (float32)
 *   2. El tipo retornado por visit() determina cómo almacenar el resultado
 *   3. allocVar registra el slot en FunctionContext con el tipo correcto
 *   4. str x0/s0 → [x29, #-offset]
 *
 * Múltiple asignación:  a, b := expr1, expr2
 *   Evalúa expr1 → guarda en a
 *   Evalúa expr2 → guarda en b
 *   (orden secuencial, no paralelo como en Go)
 */
trait ShortVarDecl
{
    public function visitShortVarDecl($ctx)
    {
        $idList   = $ctx->idList();
        $exprList = $ctx->expressionList();
        $line     = $ctx->getStart()->getLine();
        $col      = $ctx->getStart()->getCharPositionInLine();

        // Contar expresiones disponibles
        $exprCount = 0;
        for ($i = 0; $i < $exprList->getChildCount(); $i += 2) $exprCount++;

        $idx = 0;
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name = $idList->getChild($i)->getText();

            if ($idx < $exprCount) {
                $exprCtx  = $exprList->getChild($idx * 2);
                $this->comment("$name := expr (tipo inferido)");

                // Evaluar expresión → resultado en x0 o s0
                $exprType = $this->visit($exprCtx) ?? 'int32';

                // Registrar la variable con el tipo inferido
                $offset = $this->allocVar($name, $exprType, $line, $col);
                if ($offset !== null) {
                    $this->storeInferredResult($exprType, $offset);
                    $this->func->setType($name, $exprType);
                    $this->addSymbol($name, $exprType, $this->func->name, null, $line, $col);
                }
            }
            $idx++;
        }
        return null;
    }

    // ── Helper ────────────────────────────────────────────────────────────

    /**
     * Almacena el resultado en el frame usando el registro correcto
     * según el tipo inferido de la expresión.
     */
    private function storeInferredResult(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("str s0, [x29, #-$offset]", "guardar $type (inferido)");
        } else {
            $this->emit("str x0, [x29, #-$offset]", "guardar $type (inferido)");
        }
    }
}