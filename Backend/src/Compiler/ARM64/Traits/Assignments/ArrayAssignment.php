<?php

namespace Golampi\Compiler\ARM64\Traits\Assignments;

/**
 * ArrayAssignment — Generación ARM64 para asignaciones a arreglos
 *
 * Soporta: a[i] = expr  /  m[i][j] = expr  /  etc. (multidimensional)
 *
 * Generación de código ARM64 para indexación dinámica row-major.
 * 
 * Concepto de compiladores (Aho et al. — generación de lvalues):
 *   Una asignación a arreglo requiere calcular la dirección del elemento:
 *     addr(a[i]) = base_addr(a) + offset_dinámico(i)
 *   Para arrays multidimensionales (row-major):
 *     offset_dinámico(m[i][j]) = (i * cols + j) * sizeof(elem)
 *   donde cols = número de columnas en la siguiente dimensión
 *
 * Estrategia de generación ARM64:
 *   1. Evaluar cada índice → stack (orden inverso)
 *   2. Evaluar expr → x0
 *   3. Calcular offset dinámico de índices
 *   4. Adicionar offset base del array
 *   5. str x0, [addr]
 *
 * Convención de registros ARM64:
 *   x0  → valor a asignar
 *   x1  → offset dinámico (cálculo intermedio)
 *   x2  → dirección base del array
 *   x3  → dirección final del elemento
 */
trait ArrayAssignment
{
    public function visitArrayAssignment($ctx)
    {
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        // ── Validaciones semánticas ────────────────────────────────────────
        if (!$this->func) {
            $this->addError('Semántico', "No hay contexto de función", $line, $col);
            return null;
        }

        if (!$this->func->hasArray($name)) {
            $this->addError('Semántico', "Array '$name' no declarado", $line, $col);
            return null;
        }

        $arrayInfo = $this->func->getArrayInfo($name);
        if ($arrayInfo === null) {
            $this->addError('Semántico', "Array '$name' no tiene información de tipo", $line, $col);
            return null;
        }

        $op = $ctx->assignOp()->getText();
        $elemType = $arrayInfo['elem_type'];
        $dims = $arrayInfo['dims'];

        // ── Extraer índices ────────────────────────────────────────────────
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

        // Último índice es la expresión value, no un índice de array
        $valueExpr = array_pop($indices);

        // Validar cantidad de índices
        if (count($indices) !== count($dims)) {
            $this->addError('Semántico', 
                "Array '$name' requiere " . count($dims) . " índices, se proporcionaron " . count($indices),
                $line, $col);
            return null;
        }

        $this->comment("$name" . $this->indicesAsString($indices) . " $op expr");

        // ── Evaluar y guardar cada índice ──────────────────────────────────
        // Estrategia: evaluar en orden y guardar en stack
        foreach ($indices as $idxExpr) {
            $this->visit($idxExpr);  // Resultado en x0
            $this->pushStack();       // Guardar índice en stack
        }

        // ── Evaluar la expresión de asignación ─────────────────────────────
        $valueType = $this->visit($valueExpr);

        // ── Manejar compound assignments (+=, -=, *=, /=) ───────────────────
        if ($op !== '=') {
            // Para +=, etc., necesito:
            //   1. Calcular dirección con los índices guardados
            //   2. Leer el valor actual
            //   3. Aplicar la operación
            //   4. Escribir el resultado

            // Recuperar índices del stack (en orden inverso)
            $indicesFromStack = [];
            for ($i = 0; $i < count($indices); $i++) {
                $this->emit('ldr x1, [sp]', "índice $i ← stack");
                $this->emit('add sp, sp, #16');
                array_unshift($indicesFromStack, 'x1');  // Se simula, realmente necesitamos guardarlos propiamente
            }

            // Por simplicidad en Fase 3, rechazar compound assignments de arrays por ahora
            $this->addError('Generación', "Asignaciones compuestas +=, -=, etc. a arrays no soportadas aún", $line, $col);
            return null;
        }

        // ── Calcular offset dinámico ───────────────────────────────────────
        // Recuperar índices del stack en orden correcto
        $this->computeArrayOffset($indices, $dims);
        // Ahora x1 contiene el offset dinámico en bytes

        // ── Calcular dirección final ───────────────────────────────────────
        $baseOffset = $arrayInfo['base_offset'];
        $this->emit("sub x2, x29, #$baseOffset", "dirección base del array $name → x2");
        $this->emit('add x3, x2, x1', "x3 = base + offset_dinámico");

        // ── Guardar el valor ───────────────────────────────────────────────
        // x0 contiene el valor de la expresión
        // x3 contiene la dirección del elemento
        $this->emit('str x0, [x3]', "$name" . $this->indicesAsString($indices) . " ← valor");

        return $elemType;
    }

    /**
     * Calcula el offset dinámico de un array multidimensional.
     * 
     * Asume que los índices están guardados en el stack (en orden de evaluación).
     * Genera instrucciones para recuperarlos y calcular:
     *   offset = (idx[0] * dims[1] * dims[2] * ... + idx[1] * dims[2] * ... + ...) * 8
     * 
     * Postcondición: x1 = offset dinámico en bytes
     */
    private function computeArrayOffset(array $indices, array $dims): void
    {
        $numIndices = count($indices);

        if ($numIndices === 1) {
            // 1D: offset = idx[0] * 8
            $this->emit('ldr x1, [sp]', 'índice 0 ← stack');
            $this->emit('add sp, sp, #16');
            $this->emit('lsl x1, x1, #3', 'offset = idx * 8');
            return;
        }

        // Multidimensional: row-major
        // offset = (idx[0]*strides[0] + idx[1]*strides[1] + ...) * 8
        // donde strides es el producto de dimensiones posteriores
        
        $this->emit('mov x1, xzr', 'x1 = offset acumulador');

        for ($i = 0; $i < $numIndices; $i++) {
            $this->emit('ldr x4, [sp]', "índice $i ← stack");
            $this->emit('add sp, sp, #16');

            // Calcular stride (producto de dimensiones posteriores)
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

        // Convertir a bytes: offset *= 8
        $this->emit('lsl x1, x1, #3', 'offset *= 8 (sizeof element)');
    }

    /**
     * Formatea los índices como string para comentarios.
     * Ejemplo: [0][5][2]
     */
    private function indicesAsString(array $indices): string
    {
        $result = '';
        for ($i = 0; $i < count($indices); $i++) {
            $result .= '[idx' . $i . ']';
        }
        return $result;
    }
}