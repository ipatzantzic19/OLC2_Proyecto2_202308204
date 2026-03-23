<?php

namespace Golampi\Traits;

use Golampi\Runtime\Value;

/**
 * Trait para manejar arreglos en el intérprete Golampi.
 *
 * FIXES aplicados en esta versión:
 *  - FIX 1 (gramática): trailing comma en innerLiteralList → en Golampi.g4
 *  - FIX 2 (visitor):   innerLiteralList con coma final ya no rompe el visitor
 *  - FIX 3 (visitor):   visitArrayAccess y visitArrayAssignment auto-desreferencian
 *                        punteros a arreglos (*[N]T)
 */
trait ArrayVisitor
{
    // =========================================================
    //  DETECCIÓN DE TIPO ARREGLO
    // =========================================================

    protected function isArrayTypeCtx($typeCtx): bool
    {
        if ($typeCtx === null) {
            return false;
        }
        return str_starts_with(trim($typeCtx->getText()), '[');
    }

    // =========================================================
    //  CONSTRUCCIÓN DE ARREGLOS DESDE TIPO
    // =========================================================

    /**
     * Crea un Value 'array' con valores por defecto a partir de un
     * contexto de tipo arreglo ([N]type).  Soporta cualquier nivel
     * de multidimensionalidad.
     */
    protected function createArrayFromTypeCtx($typeCtx): Value
    {
        // SliceType ([]T): sin tamaño fijo → arreglo vacío por defecto
        if ($typeCtx->expression() === null) {
            $innerTypeCtx = $typeCtx->type();
            $isNested     = $this->isArrayTypeCtx($innerTypeCtx);
            $elementType  = $isNested ? 'array' : $this->extractType($innerTypeCtx);
            return new Value('array', [
                'elementType' => $elementType,
                'size'        => 0,
                'elements'    => [],
            ]);
        }

        $sizeValue = $this->visit($typeCtx->expression());
        $size      = (int) $sizeValue->getValue();

        if ($size <= 0) {
            $this->addSemanticError(
                "El tamaño del arreglo debe ser un entero positivo, se obtuvo: $size",
                $typeCtx->getStart()->getLine(),
                $typeCtx->getStart()->getCharPositionInLine()
            );
            $size = 0;
        }

        $innerTypeCtx  = $typeCtx->type();
        $isNestedArray = $this->isArrayTypeCtx($innerTypeCtx);
        $elementType   = $isNestedArray ? 'array' : $this->extractType($innerTypeCtx);

        $elements = [];
        for ($i = 0; $i < $size; $i++) {
            $elements[] = $isNestedArray
                ? $this->createArrayFromTypeCtx($innerTypeCtx)
                : $this->getDefaultValue($elementType);
        }

        return new Value('array', [
            'elementType' => $elementType,
            'size'        => $size,
            'elements'    => $elements,
        ]);
    }

    /**
     * Crea un arreglo desde dimensiones explícitas + tipo base.
     */
    protected function createArrayFromDims(array $dims, string $baseType, ?array $initVals = null): Value
    {
        $size          = $dims[0];
        $remainingDims = array_slice($dims, 1);
        $isNested      = count($remainingDims) > 0;
        $elementType   = $isNested ? 'array' : $baseType;

        $elements = [];
        for ($i = 0; $i < $size; $i++) {
            if ($initVals !== null && isset($initVals[$i])) {
                $el = $initVals[$i];
                if ($el->getType() === 'array') {
                    $elements[] = $el;
                    continue;
                }
            }

            if ($isNested) {
                $elements[] = $this->createArrayFromDims($remainingDims, $baseType);
            } else {
                $elements[] = ($initVals !== null && isset($initVals[$i]))
                    ? $initVals[$i]
                    : $this->getDefaultValue($baseType);
            }
        }

        return new Value('array', [
            'elementType' => $elementType,
            'size'        => $size,
            'elements'    => $elements,
        ]);
    }

    // =========================================================
    //  LITERALES DE ARREGLO
    // =========================================================

    public function visitArrayLiteralExpr($context)
    {
        // Despacha a visitFixedArrayLiteralNode o visitSliceLiteralNode según la alternativa
        return $this->visit($context->arrayLiteral());
    }

    // =========================================================
    //  TIPO SLICE ([]T) — solo marca el tipo, helpers lo leen via getText()
    // =========================================================

    public function visitSliceType($context)
    {
        return null;
    }

    // =========================================================
    //  LITERAL SLICE: []T{ e1, e2, ... }
    // =========================================================

    public function visitSliceLiteralNode($context)
    {
        $typeCtx     = $context->type();
        $isNested    = $this->isArrayTypeCtx($typeCtx);
        $elementType = $isNested ? 'array' : $this->extractType($typeCtx);
        $elements    = [];

        if ($context->expressionList() !== null) {
            $exprList = $context->expressionList();
            for ($i = 0; $i < $exprList->getChildCount(); $i += 2) {
                $elements[] = $this->visit($exprList->getChild($i));
            }
        }

        return new Value('array', [
            'elementType' => $elementType,
            'size'        => count($elements),
            'elements'    => $elements,
        ]);
    }

    /**
     * Procesa:  '[' expression ']' type '{' (innerLiteralList | expressionList)? ','? '}'
     *
     * FIX 2: el visitor itera correctamente los hijos de innerLiteralList
     *        ignorando las comas y las llaves separadoras.
     */
    public function visitFixedArrayLiteralNode($context)
    {
        $sizeValue = $this->visit($context->expression());
        $size      = (int) $sizeValue->getValue();

        $typeCtx       = $context->type();
        $isNestedArray = $this->isArrayTypeCtx($typeCtx);
        $elementType   = $isNestedArray ? 'array' : $this->extractType($typeCtx);

        $elements = [];

        // ── Inicialización con literales internos {{…},{…},...} ───────
        if ($context->innerLiteralList() !== null) {
            $innerList = $context->innerLiteralList()->innerLiteral();
            foreach ($innerList as $inner) {
                $elements[] = $this->buildInnerArray($inner->expressionList(), $typeCtx);
            }
        }
        // ── Inicialización plana {e1, e2, …} ─────────────────────────
        elseif ($context->expressionList() !== null) {
            $exprList = $context->expressionList();
            for ($i = 0; $i < $exprList->getChildCount(); $i += 2) {
                $elements[] = $this->visit($exprList->getChild($i));
            }
        }

        // Completar con valores por defecto hasta $size
        while (count($elements) < $size) {
            $elements[] = $isNestedArray
                ? $this->createArrayFromTypeCtx($typeCtx)
                : $this->getDefaultValue($elementType);
        }

        return new Value('array', [
            'elementType' => $elementType,
            'size'        => $size,
            'elements'    => $elements,
        ]);
    }

    /**
     * Construye un sub-arreglo interno para matrices multidimensionales.
     */
    private function buildInnerArray($expressionListCtx, $expectedTypeCtx): Value
    {
        $elements = [];

        if ($expressionListCtx !== null) {
            for ($i = 0; $i < $expressionListCtx->getChildCount(); $i += 2) {
                $elements[] = $this->visit($expressionListCtx->getChild($i));
            }
        }

        $isNested    = $this->isArrayTypeCtx($expectedTypeCtx);
        $elementType = $isNested ? 'array' : $this->extractType($expectedTypeCtx);

        return new Value('array', [
            'elementType' => $elementType,
            'size'        => count($elements),
            'elements'    => $elements,
        ]);
    }

    // =========================================================
    //  ACCESO A ELEMENTOS (LECTURA)
    // =========================================================

    /**
     * Visita:  ID '[' expr ']' ('[' expr ']')*
     *
     * FIX 3: si la variable es un puntero a arreglo (*[N]T),
     *        se desreferencia automáticamente antes de indexar.
     */
    public function visitArrayAccess($context)
    {
        $varName = $context->ID()->getText();
        $line    = $context->getStart()->getLine();
        $col     = $context->getStart()->getCharPositionInLine();

        $arr = $this->environment->get($varName);

        if ($arr === null) {
            $this->addSemanticError("Variable '$varName' no declarada", $line, $col);
            return Value::nil();
        }

        // FIX 3 ── auto-desreferenciar puntero a arreglo ───────────────
        if ($arr->getType() === 'pointer') {
            $data = $arr->getValue();
            $arr  = $data['env']->get($data['varName']);

            if ($arr === null) {
                $this->addSemanticError(
                    "Puntero '$varName' apunta a una variable no válida", $line, $col
                );
                return Value::nil();
            }
        }

        if ($arr->getType() !== 'array') {
            $this->addSemanticError(
                "La variable '$varName' no es un arreglo (tipo: '{$arr->getType()}')",
                $line, $col
            );
            return Value::nil();
        }

        // Evaluar todos los índices
        $indices = [];
        foreach ($context->expression() as $exprCtx) {
            $idxVal = $this->visit($exprCtx);
            if ($idxVal === null || !in_array($idxVal->getType(), ['int32', 'rune'])) {
                $this->addSemanticError(
                    "El índice del arreglo debe ser de tipo int32 o rune",
                    $exprCtx->getStart()->getLine(),
                    $exprCtx->getStart()->getCharPositionInLine()
                );
                return Value::nil();
            }
            $indices[] = (int) $idxVal->getValue();
        }

        return $this->getArrayElement($arr, $indices, $varName, $line, $col);
    }

    // =========================================================
    //  ACCESO A ELEMENTOS (ESCRITURA)
    // =========================================================

    /**
     * Visita:  ID ('[' expr ']')+ assignOp expression
     *
     * FIX 3: si la variable es un puntero a arreglo (*[N]T),
     *        se desreferencia automáticamente antes de escribir.
     *        La escritura modifica el arreglo original a través del puntero.
     */
    public function visitArrayAssignment($context)
    {
        $varName  = $context->ID()->getText();
        $assignOp = $context->assignOp()->getText();
        $line     = $context->getStart()->getLine();
        $col      = $context->getStart()->getCharPositionInLine();

        $varValue = $this->environment->get($varName);

        if ($varValue === null) {
            $this->addSemanticError("Variable '$varName' no declarada", $line, $col);
            return null;
        }

        // FIX 3 ── resolver puntero a arreglo ──────────────────────────
        $isPointer      = ($varValue->getType() === 'pointer');
        $targetVarName  = $varName;
        $targetEnv      = $this->environment;

        if ($isPointer) {
            $ptrData       = $varValue->getValue();
            $targetVarName = $ptrData['varName'];
            $targetEnv     = $ptrData['env'];
            $varValue      = $targetEnv->get($targetVarName);

            if ($varValue === null) {
                $this->addSemanticError(
                    "Puntero '$varName' apunta a una variable no válida", $line, $col
                );
                return null;
            }
        }

        $arr = $varValue;

        if ($arr->getType() !== 'array') {
            $this->addSemanticError(
                "La variable '$varName' no es un arreglo (tipo: '{$arr->getType()}')",
                $line, $col
            );
            return null;
        }

        // Separar índices del valor (última expresión = valor)
        $allExprs   = $context->expression();
        $totalExprs = count($allExprs);
        $indexExprs = array_slice($allExprs, 0, $totalExprs - 1);
        $valueExpr  = $allExprs[$totalExprs - 1];

        // Evaluar índices
        $indices = [];
        foreach ($indexExprs as $idxCtx) {
            $idxVal = $this->visit($idxCtx);
            if ($idxVal === null || !in_array($idxVal->getType(), ['int32', 'rune'])) {
                $this->addSemanticError(
                    "El índice del arreglo debe ser de tipo int32 o rune",
                    $idxCtx->getStart()->getLine(),
                    $idxCtx->getStart()->getCharPositionInLine()
                );
                return null;
            }
            $indices[] = (int) $idxVal->getValue();
        }

        // Evaluar nuevo valor
        $newValue = $this->visit($valueExpr);

        // Para asignaciones compuestas, leer el valor actual primero
        if ($assignOp !== '=') {
            $currentEl = $this->getArrayElement($arr, $indices, $varName, $line, $col);
            if ($currentEl === null) return null;

            $newValue = match ($assignOp) {
                '+=' => $this->performAddition($currentEl, $newValue, $line, $col),
                '-=' => $this->performSubtraction($currentEl, $newValue, $line, $col),
                '*=' => $this->performMultiplication($currentEl, $newValue, $line, $col),
                '/=' => $this->performDivision($currentEl, $newValue, $line, $col),
                default => $newValue,
            };
        }

        // Mutar el arreglo en su lugar
        $success = $this->setArrayElement($arr, $indices, $newValue, $varName, $line, $col);

        if ($success) {
            // Si era puntero, actualizar en el entorno original
            if ($isPointer) {
                $targetEnv->set($targetVarName, $arr);
                $this->updateSymbolValue($targetVarName, $arr);
            } else {
                $this->updateSymbolValue($varName, $arr);
            }
        }

        return null;
    }

    // =========================================================
    //  HELPERS DE LECTURA / ESCRITURA
    // =========================================================

    protected function getArrayElement(
        Value  $arr,
        array  $indices,
        string $varName = '?',
        int    $line    = 0,
        int    $col     = 0
    ): Value {
        $current = $arr;

        foreach ($indices as $idx) {
            if ($current->getType() !== 'array') {
                $this->addSemanticError(
                    "Acceso de índice en un valor que no es arreglo (variable '$varName')",
                    $line, $col
                );
                return Value::nil();
            }

            $data = $current->getValue();
            $size = $data['size'];

            if ($idx < 0 || $idx >= $size) {
                $this->addSemanticError(
                    "Índice $idx fuera de rango (tamaño: $size) en '$varName'",
                    $line, $col
                );
                return Value::nil();
            }

            $current = $data['elements'][$idx];
        }

        return $current;
    }

    protected function setArrayElement(
        Value  $arr,
        array  $indices,
        Value  $newValue,
        string $varName = '?',
        int    $line    = 0,
        int    $col     = 0
    ): bool {
        if (count($indices) === 1) {
            $data = $arr->getValue();
            $idx  = $indices[0];

            if ($idx < 0 || $idx >= $data['size']) {
                $this->addSemanticError(
                    "Índice $idx fuera de rango (tamaño: {$data['size']}) en '$varName'",
                    $line, $col
                );
                return false;
            }

            $data['elements'][$idx] = $newValue;
            $arr->setValue($data);
            return true;
        }

        // Caso recursivo: navegar hacia el sub-arreglo
        $data = $arr->getValue();
        $idx  = $indices[0];

        if ($idx < 0 || $idx >= $data['size']) {
            $this->addSemanticError(
                "Índice $idx fuera de rango (tamaño: {$data['size']}) en '$varName'",
                $line, $col
            );
            return false;
        }

        $subArr = $data['elements'][$idx];

        if ($subArr->getType() !== 'array') {
            $this->addSemanticError(
                "Acceso de índice en un valor que no es arreglo (variable '$varName')",
                $line, $col
            );
            return false;
        }

        $success = $this->setArrayElement(
            $subArr,
            array_slice($indices, 1),
            $newValue,
            $varName,
            $line,
            $col
        );

        if ($success) {
            $data['elements'][$idx] = $subArr;
            $arr->setValue($data);
        }

        return $success;
    }

    // =========================================================
    //  FORMATO PARA SALIDA / TABLA DE SÍMBOLOS
    // =========================================================

    public function arrayToString(Value $arr, bool $compact = false): string
    {
        if ($arr->getType() !== 'array') {
            return $arr->toString();
        }

        $data  = $arr->getValue();
        $parts = [];

        foreach ($data['elements'] as $el) {
            $parts[] = ($el->getType() === 'array')
                ? $this->arrayToString($el, $compact)
                : $el->toString();
        }

        return $compact
            ? '{' . implode(',', $parts) . '}'
            : '[' . implode(' ', $parts) . ']';
    }
}