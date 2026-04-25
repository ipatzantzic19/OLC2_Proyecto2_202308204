<?php

namespace Golampi\Compiler\ARM64\Traits\Expressions;

/**
 * UnaryOpsTrait — Generación ARM64 para operadores unarios
 *
 * Implementa todos los operadores unarios del lenguaje Golampi:
 *
 *   -expr     → negación aritmética (int32 o float32)
 *   !expr     → negación lógica (bool)
 *   &ID       → dirección de memoria (address-of)
 *   *expr     → desreferencia de puntero
 *   (expr)    → agrupación (passthrough)
 *
 * Generación ARM64:
 *
 *   -x  (int32):   neg x0, x0
 *   -x  (float32): fneg s0, s0
 *   !x  (bool):    eor x0, x0, #1   (XOR con 1 invierte el bit menos significativo)
 *   &ID:           sub x0, x29, #offset  (dirección relativa al frame pointer)
 *   *ptr:          ldr x0, [x0]          (carga el valor apuntado)
 *
 * Nota sobre address-of (&ID):
 *   En ARM64, las variables locales viven en el stack relativo a x29 (fp).
 *   La dirección de la variable x con offset N es: x29 - N
 *   Instrucción: sub x0, x29, #N  (subtract immediate del fp)
 *
 * Nota sobre dereference (*ptr):
 *   Fase 2: asumimos que el puntero apunta a int32 (ldr x0, [x0]).
 *   Fase 3 ampliará esto para tipos float, arrays, etc.
 *
 * También incluye el passthrough de visitPrimaryUnary y visitGroupedExpression
 * por cohesión (ambos delegan sin transformación).
 */
trait UnaryOps
{
    // ── Passthrough de unario primario ────────────────────────────────────

    public function visitPrimaryUnary($ctx)
    {
        return $this->visit($ctx->primary());
    }

    // ── Negación aritmética: -expr ────────────────────────────────────────

    public function visitNegativeUnary($ctx)
    {
        $type = $this->visit($ctx->unary());

        if ($type === 'float32') {
            // Negación de registro SIMD: fneg s0, s0
            $this->emit('fneg s0, s0', 'negación float32');
        } else {
            // Negación entera: neg w0, w0  (equivale a rsb w0, w0, #0)
            $this->emit('neg x0, x0', 'negación int32');
        }

        return $type;
    }

    // ── Negación lógica: !expr ────────────────────────────────────────────

    public function visitNotUnary($ctx)
    {
        $this->visit($ctx->unary());
        // XOR del bit menos significativo: invierte true↔false (bool es 32-bit)
        // 0 XOR 1 = 1  (false → true)
        // 1 XOR 1 = 0  (true  → false)
        $this->emit('eor x0, x0, #1', 'NOT lógico');
        return 'bool';
    }

    // ── Address-of: &ID ──────────────────────────────────────────────────

    /**
     * Genera la dirección de una variable local del frame.
     * Equivale a tomar la dirección relativa al frame pointer x29.
     *
     * sub x0, x29, #offset  →  x0 = x29 - offset = dirección de la var
     */
    public function visitAddressOf($ctx)
    {
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            $this->emit('mov x0, xzr', '&? → nil (error)');
            return 'pointer';
        }

        $offset = $this->func->getOffset($name);
        $this->emit("sub x0, x29, #$offset", "&$name → dirección en frame");
        return 'pointer';
    }

    // ── Dereference: *expr ────────────────────────────────────────────────

    /**
     * Desreferencia un puntero: carga el valor desde la dirección en x0.
     * Fase 2: asume que el tipo apuntado es int32 (ldr de 64 bits).
     */
    public function visitDereference($ctx)
    {
        $this->visit($ctx->unary());
        // x0 contiene la dirección → cargar el valor apuntado
        $this->emit('ldr x0, [x0]', '*ptr → valor en x0');
        return 'int32';
    }

    // ── Agrupación: (expr) ────────────────────────────────────────────────

    public function visitGroupedExpression($ctx)
    {
        return $this->visit($ctx->expression());
    }

    // ── Arrays (Fase 3 — Lectura y literales) ────────────────────────────────

    /**
     * Acceso a elemento de array: ID ('[' expression ']')+
     * 
     * Ejemplo: a[i], m[i][j]
     * 
     * Estrategia:
     *   1. Evaluar cada índice → stack
     *   2. Calcular offset dinámico (row-major)
     *   3. Cargar dirección base del array
     *   4. Calcular dirección del elemento
     *   5. ldr x0, [dirección] → resultado
     */
    public function visitArrayAccess($ctx)
    {
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func) {
            $this->addError('Semántico', "No hay contexto de función", $line, $col);
            $this->emit('mov x0, xzr', 'array access error → 0');
            return 'int32';
        }

        if (!$this->func->hasArray($name)) {
            $this->addError('Semántico', "Array '$name' no declarado", $line, $col);
            $this->emit('mov x0, xzr', "array $name no encontrado");
            return 'int32';
        }

        $arrayInfo = $this->func->getArrayInfo($name);
        if ($arrayInfo === null) {
            $this->addError('Semántico', "Array '$name' no tiene información", $line, $col);
            $this->emit('mov x0, xzr');
            return 'int32';
        }

        $elemType = $arrayInfo['elem_type'];
        $dims = $arrayInfo['dims'];

        // ── Extraer índices ───────────────────────────────────────────────
        $indices = [];
        for ($i = 0; $i < $ctx->getChildCount(); $i++) {
            $child = $ctx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $class = get_class($child);
                if (str_ends_with($class, 'ExpressionContext')) {
                    $indices[] = $child;
                }
            }
        }

        // Validar cantidad de índices
        if (count($indices) !== count($dims)) {
            $this->addError('Semántico',
                "Array '$name' requiere " . count($dims) . " índices, se proporcionaron " . count($indices),
                $line, $col);
            $this->emit('mov x0, xzr');
            return $elemType;
        }

        $this->comment("$name" . $this->formatArrayIndices($indices) . " (lectura)");

        // ── Evaluar y guardar cada índice ─────────────────────────────────
        foreach ($indices as $idxExpr) {
            $this->visit($idxExpr);  // Resultado en x0
            $this->pushStack();       // Guardar índice en stack
        }

        // ── Calcular offset dinámico ──────────────────────────────────────
        $this->computeReadArrayOffset($indices, $dims);
        // x1 contiene el offset dinámico en bytes

        // ── Calcular dirección final ──────────────────────────────────────
        $baseOffset = $arrayInfo['base_offset'];
        $this->emit("sub x2, x29, #$baseOffset", "dirección base del array $name → x2");
        $this->emit('sub x3, x2, x1', "x3 = base - offset_dinámico");

        // ── Cargar el valor ───────────────────────────────────────────────
        if ($elemType === 'float32') {
            $this->emit('ldr s0, [x3]', $name . '[idx] (lectura) → s0 (float32)');
        } else {
            $this->emit('ldr x0, [x3]', $name . '[idx] (lectura) → x0');
        }

        return $elemType;
    }

    /**
     * Literal de array: [N]type{...}
     * 
     * Fase 3: Generación de inicializadores de arrays.
     * Para literales fijos como [10]int32 { 1, 2, 3, ... }
     */
    public function visitArrayLiteralExpr($ctx)
    {
        $literalInfo = $this->collectArrayLiteralValues($ctx->arrayLiteral());

        if ($literalInfo === null) {
            $this->emit('mov x0, xzr', 'array literal — sin información suficiente');
            return 'array';
        }

        $this->lastArrayLiteral = $literalInfo;

        if (!empty($this->pendingArrayInitName)) {
            $this->initializeArrayLiteralInMemory($this->pendingArrayInitName, $ctx);
            $this->pendingArrayInitName = null;
        }

        // El valor resultante de un literal de array es el array mismo.
        $this->emit('mov x0, xzr', 'array literal → valor manejado por el destino');
        return 'array';
    }

    /**
    * Devuelve información plana del literal de array.
    * Estructura: ['values' => array<array{kind:string,value:mixed}>, 'dims' => int[]]
     */
    protected function collectArrayLiteralValues($arrayLiteralCtx): ?array
    {
        if ($arrayLiteralCtx === null) {
            return null;
        }

        if (is_callable([$arrayLiteralCtx, 'arrayLiteral'])) {
            try {
                $nested = $arrayLiteralCtx->arrayLiteral();
                if ($nested !== null) {
                    $arrayLiteralCtx = $nested;
                }
            } catch (\Throwable $e) {}
        }

        $values = [];
        $dims = [];

        $appendScalarValues = function ($exprListCtx) use (&$values): void {
            if ($exprListCtx === null || !method_exists($exprListCtx, 'getChildCount')) {
                return;
            }

            for ($i = 0; $i < $exprListCtx->getChildCount(); $i += 2) {
                $expr = $exprListCtx->getChild($i);
                $value = $this->evaluateArrayLiteralScalar($expr);
                if ($value !== null) {
                    $values[] = $value;
                }
            }
        };

        $class = get_class($arrayLiteralCtx);
        $base  = substr($class, strrpos($class, '\\') + 1);

        if ($base === 'FixedArrayLiteralNodeContext') {
            $firstDimExpr = $arrayLiteralCtx->expression();
            $firstDim = $this->evaluateArrayLiteralScalar($firstDimExpr);
            if ($firstDim === null || $firstDim <= 0) {
                return null;
            }

            $dims[] = $firstDim;
            $typeCtx = $arrayLiteralCtx->type();
            if ($typeCtx !== null) {
                $tailDims = $this->extractArrayDimsForLiteral($typeCtx);
                $dims = array_merge($dims, $tailDims);
            }

            $exprList = $arrayLiteralCtx->expressionList();
            $innerList = $arrayLiteralCtx->innerLiteralList();

            if ($exprList !== null) {
                $appendScalarValues($exprList);
            } elseif ($innerList !== null) {
                for ($i = 0; $i < $innerList->getChildCount(); $i++) {
                    $child = $innerList->getChild($i);
                    if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                        if (method_exists($child, 'expressionList')) {
                            $appendScalarValues($child->expressionList());
                        }
                    }
                }
            }

            return ['values' => $values, 'dims' => $dims];
        }

        if ($base === 'SliceLiteralNodeContext') {
            $exprList = $arrayLiteralCtx->expressionList();
            if ($exprList !== null) {
                $appendScalarValues($exprList);
                $dims[] = count($values);
                return ['values' => $values, 'dims' => $dims];
            }
        }

        return null;
    }

    /**
     * Evalúa un literal escalar de un literal de arreglo.
     */
    protected function evaluateArrayLiteralScalar($exprCtx)
    {
        if ($exprCtx === null) {
            return null;
        }

        if ($exprCtx instanceof \Antlr\Antlr4\Runtime\TerminalNode) {
            $text = $exprCtx->getText();
            if (is_numeric($text)) {
                return ['kind' => 'int32', 'value' => (int) $text];
            }
            if ($text === 'true') {
                return ['kind' => 'bool', 'value' => 1];
            }
            if ($text === 'false') {
                return ['kind' => 'bool', 'value' => 0];
            }
            if (strlen($text) >= 2 && $text[0] === '"' && substr($text, -1) === '"') {
                return ['kind' => 'string', 'value' => stripcslashes(substr($text, 1, -1))];
            }
            if (strlen($text) >= 3 && $text[0] === '\'' && substr($text, -1) === '\'') {
                return ['kind' => 'rune', 'value' => ord($text[1])];
            }
        }

        if (method_exists($exprCtx, 'getChildCount')) {
            for ($i = 0; $i < $exprCtx->getChildCount(); $i++) {
                $child = $exprCtx->getChild($i);
                $value = $this->evaluateArrayLiteralScalar($child);
                if ($value !== null) {
                    return $value;
                }
            }
        }

        $class = get_class($exprCtx);
        $base  = substr($class, strrpos($class, '\\') + 1);

        if ($base === 'IntLiteralContext') {
            $token = $exprCtx->INT32();
            return $token ? ['kind' => 'int32', 'value' => (int) $token->getText()] : null;
        }

        if ($base === 'TrueLiteralContext') {
            return ['kind' => 'bool', 'value' => 1];
        }

        if ($base === 'FalseLiteralContext') {
            return ['kind' => 'bool', 'value' => 0];
        }

        if ($base === 'RuneLiteralContext') {
            $text = $exprCtx->RUNE()->getText();
            return strlen($text) >= 3 ? ['kind' => 'rune', 'value' => ord($text[1])] : null;
        }

        if ($base === 'FloatLiteralContext') {
            return ['kind' => 'float32', 'value' => (float) $exprCtx->FLOAT32()->getText()];
        }

        if ($base === 'StringLiteralContext') {
            $text = $exprCtx->STRING()->getText();
            return ['kind' => 'string', 'value' => stripcslashes(substr($text, 1, -1))];
        }

        return null;
    }

    /**
     * Extrae dimensiones desde un tipo anidado para un array literal.
     */
    private function extractArrayDimsForLiteral($typeCtx): array
    {
        $dims = [];
        $current = $typeCtx;

        while ($current !== null) {
            $class = get_class($current);
            $base = substr($class, strrpos($class, '\\') + 1);

            if ($base !== 'ArrayTypeContext') {
                break;
            }

            $expr = null;
            if (is_callable([$current, 'expression'])) {
                try {
                    $expr = $current->expression();
                } catch (\Throwable $e) {}
            }

            $dim = $this->evaluateArrayLiteralScalar($expr);
            if ($dim === null || $dim <= 0) {
                break;
            }

            $dims[] = $dim;

            if (is_callable([$current, 'type'])) {
                try {
                    $current = $current->type();
                } catch (\Throwable $e) {
                    $current = null;
                }
            } else {
                $current = null;
            }
        }

        return $dims;
    }

    /**
     * Inicializa un array ya registrado en memoria con un literal.
     */
    protected function initializeArrayLiteralInMemory(string $name, $arrayLiteralCtx): void
    {
        if (!$this->func || !$this->func->hasArray($name)) {
            return;
        }

        if (is_callable([$arrayLiteralCtx, 'arrayLiteral'])) {
            try {
                $nested = $arrayLiteralCtx->arrayLiteral();
                if ($nested !== null) {
                    $arrayLiteralCtx = $nested;
                }
            } catch (\Throwable $e) {}
        }

        $info = $this->collectArrayLiteralValues($arrayLiteralCtx);
        if ($info === null) {
            return;
        }

        $arrayInfo = $this->func->getArrayInfo($name);
        if ($arrayInfo === null) {
            return;
        }

        $baseOffset = $arrayInfo['base_offset'];
        $elemType = $arrayInfo['elem_type'] ?? 'int32';
        $totalSlots = $arrayInfo['total_slots'] ?? count($info['values']);

        $values = $info['values'];
        for ($i = 0; $i < $totalSlots; $i++) {
            $entry = $values[$i] ?? ['kind' => $elemType, 'value' => 0];
            $offset = $baseOffset + ($i * 8);

            if ($entry['kind'] === 'string') {
                $label = $this->internString((string) $entry['value']);
                $this->emit("adrp x0, $label");
                $this->emit("add x0, x0, :lo12:$label");
                $this->emit("str x0, [x29, #-$offset]", $name . "[$i] ← string literal");
            } elseif ($entry['kind'] === 'float32') {
                $this->emit('mov x0, xzr', $name . "[$i] ← float placeholder");
                $this->emit("str x0, [x29, #-$offset]");
            } else {
                $this->emit('mov x0, #' . (int) $entry['value'], $name . "[$i] ← literal");
                $this->emit("str x0, [x29, #-$offset]");
            }
        }
    }

    /**
     * Formatea los índices como string para comentarios.
     * Ejemplo: [0][5][2]
     */
    private function formatArrayIndices(array $indices): string
    {
        $result = '';
        for ($i = 0; $i < count($indices); $i++) {
            $result .= '[idx' . $i . ']';
        }
        return $result;
    }

    /**
     * Calcula el offset dinámico de un array multidimensional (para lectura).
     * (Mismo algoritmo que ArrayAssignment::computeArrayOffset)
     * 
     * Precondición: índices guardados en stack
     * Postcondición: x1 = offset dinámico en bytes
     */
    private function computeReadArrayOffset(array $indices, array $dims): void
    {
        $numIndices = count($indices);

        if ($numIndices === 1) {
            $this->emit('ldr x1, [sp]', 'índice 0 ← stack');
            $this->emit('add sp, sp, #16');
            $this->emit('lsl x1, x1, #3', 'offset = idx * 8');
            return;
        }

        // Multidimensional: row-major
        $this->emit('mov x1, xzr', 'x1 = offset acumulador');

        for ($i = 0; $i < $numIndices; $i++) {
            $this->emit('ldr x4, [sp]', "índice $i ← stack");
            $this->emit('add sp, sp, #16');

            $stride = 1;
            for ($j = $i + 1; $j < count($dims); $j++) {
                $stride *= $dims[$j];
            }

            if ($stride > 1) {
                $this->emit("mov x5, #$stride", "stride = $stride");
                $this->emit('mul x4, x4, x5', "idx[$i] * stride");
            }

            $this->emit('add x1, x1, x4', "offset += idx[$i] * stride");
        }

        $this->emit('lsl x1, x1, #3', 'offset *= 8');
    }
}