<?php

namespace Golampi\Compiler\ARM64\Traits\Declarations;

/**
 * ShortVarDecl — Generación ARM64 para declaración corta de variables
 *
 * FIX FASE 2: soporta asignación multi-return:  q, r := divmod(10, 3)
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  CASOS SOPORTADOS
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   Caso A — Asignación 1:1          x := expr
 *   Caso B — Asignación múltiple     a, b := e1, e2
 *   Caso C — Multi-return            q, r := divmod(10, 3)   ← FIX
 *
 *   El Caso C se distingue del Caso B porque:
 *     • idCount  = 2  (N variables)
 *     • exprCount = 1  (UNA expresión que retorna tipo 'multi')
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  GENERACIÓN ARM64 PARA MULTI-RETURN (Caso C)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   q, r := divmod(10, 3)
 *
 *   1. bl divmod          → x0 = cociente, x1 = resto  (convención AArch64)
 *   2. sub sp, sp, #16
 *      str x1, [sp]       → preservar x1 antes de que str x0 lo pise (no lo pisa,
 *                            pero sp puede cambiar si allocVar re-emite instrucciones)
 *   3. str x0, [x29, #-offset_q]   → guardar primer retorno en q
 *   4. ldr x1, [sp]
 *      add sp, sp, #16
 *   5. str x1, [x29, #-offset_r]   → guardar segundo retorno en r
 *
 *   Invariante: el prescan (Prescan.php) ya asignó slots para q y r con
 *   tipo 'unknown'. allocVar() devuelve el offset existente y actualiza
 *   el tipo a 'int32' (o el tipo inferido correcto).
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  INFERENCIA DE TIPOS MULTI-RETURN
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   UserFunctionCall::inferReturnType() devuelve 'multi' cuando la función
 *   tiene FuncDeclMultiReturnContext (declaración con typeList).
 *
 *   Tipos asumidos por el proyecto para multi-return:
 *     • todos los valores van en registros enteros (x0, x1, ...)
 *     • float32 en s0, s1, ... (extensión futura)
 *
 *   Para este alcance se asume tipo 'int32' en los retornos adicionales,
 *   que es correcto para el caso canónico (int32, bool).
 */
trait ShortVarDecl
{
    public function visitShortVarDecl($ctx)
    {
        $idList   = $ctx->idList();
        $exprList = $ctx->expressionList();
        $line     = $ctx->getStart()->getLine();
        $col      = $ctx->getStart()->getCharPositionInLine();

        // ── Recolectar IDs ────────────────────────────────────────────────
        $ids = [];
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $ids[] = $idList->getChild($i)->getText();
        }
        $idCount = count($ids);

        // ── Recolectar nodos de expresión ────────────────────────────────
        // Los separadores ',' son TerminalNodes en índices impares.
        $exprNodes = [];
        for ($i = 0; $i < $exprList->getChildCount(); $i += 2) {
            $exprNodes[] = $exprList->getChild($i);
        }
        $exprCount = count($exprNodes);

        // ── Caso C: multi-return  (N ids, 1 expresión) ───────────────────
        // Detección: más identificadores que expresiones → posible multi-return.
        // Confirmación: el tipo retornado por visit() es 'multi'.
        if ($idCount > 1 && $exprCount === 1) {
            return $this->handleMultiReturn($ids, $exprNodes[0], $line, $col);
        }

        // ── Casos A y B: asignación normal 1:1 ───────────────────────────
        for ($i = 0; $i < $idCount; $i++) {
            $name = $ids[$i];

            if ($i < $exprCount) {
                $this->comment("$name := expr (tipo inferido)");
                $exprType = $this->visit($exprNodes[$i]) ?? 'int32';

                $offset = $this->allocVar($name, $exprType, $line, $col);
                if ($offset !== null) {
                    $this->storeInferredResult($exprType, $offset);
                    $this->func->setType($name, $exprType);
                    $this->addSymbol($name, $exprType,
                        $this->func->name, null, $line, $col);
                }
            }
            // Si hay más ids que exprs y no es multi-return, los ids
            // sobrantes quedan con el valor por defecto que asignó el prescan.
        }

        return null;
    }

    // ── Handler de multi-return ───────────────────────────────────────────

    /**
     * Genera código para:  q, r := funcMultiRetorno(args...)
     *
     * Tras `bl funcName`, la convención AArch64 deja:
     *   x0 = primer  valor de retorno  (int / bool / rune / string)
     *   x1 = segundo valor de retorno
     *
     * Se preserva x1 en el stack mientras se almacena x0, luego se
     * recupera x1 y se almacena en el segundo slot del frame.
     *
     * @param string[] $ids      Lista de identificadores del lado izquierdo
     * @param mixed    $exprCtx  Nodo de la expresión (llamada a función)
     * @param int      $line     Línea para reporte de errores
     * @param int      $col      Columna para reporte de errores
     */
    private function handleMultiReturn(
        array $ids,
        $exprCtx,
        int $line,
        int $col
    ): ?string {
        $this->comment('multi-asignación := (función multi-retorno)');

        // Evaluar la expresión → bl funcName → x0=val0, x1=val1
        $returnType = $this->visit($exprCtx) ?? 'int32';

        // Verificar que realmente es multi-return
        if ($returnType !== 'multi') {
            // No es multi-return: asignar solo el primer id con el resultado en x0
            $this->comment("(retorno simple inferido como '$returnType')");
            $offset0 = $this->allocVar($ids[0], $returnType, $line, $col);
            if ($offset0 !== null) {
                $this->storeInferredResult($returnType, $offset0);
                $this->func->setType($ids[0], $returnType);
                $this->addSymbol($ids[0], $returnType,
                    $this->func->name, null, $line, $col);
            }
            return null;
        }

        // ── Convención AArch64 multi-return ──────────────────────────────
        // Tras bl:  x0 = primer retorno, x1 = segundo retorno
        //
        // Estrategia (Aho et al. — descriptores de dirección):
        //   1. Preservar x1 (segundo retorno) en slot temporal del stack.
        //   2. Guardar x0 en el frame slot de ids[0].
        //   3. Recuperar x1 del stack.
        //   4. Guardar x1 en el frame slot de ids[1].

        $this->comment('preservar segundo retorno (x1) mientras se guarda x0');
        $this->emit('sub sp, sp, #16',  'slot temporal para x1');
        $this->emit('str x1, [sp]',     'x1 (segundo retorno) → stack');

        // Guardar primer retorno → ids[0]
        $type0   = 'int32'; // primer retorno: siempre entero en la convención simplificada
        $offset0 = $this->allocVar($ids[0], $type0, $line, $col);
        if ($offset0 !== null) {
            $this->emit("str x0, [x29, #-$offset0]",
                "{$ids[0]} ← x0 (primer retorno)");
            $this->func->setType($ids[0], $type0);
            $this->addSymbol($ids[0], $type0,
                $this->func->name, null, $line, $col);
        }

        // Recuperar segundo retorno desde stack → ids[1]
        $this->emit('ldr x1, [sp]',  'x1 (segundo retorno) ← stack');
        $this->emit('add sp, sp, #16', 'liberar slot temporal');

        if (isset($ids[1])) {
            // El segundo retorno puede ser bool (1/0 en x1) o int32.
            // Lo tratamos como 'int32' (bool se almacena igual en ARM64).
            $type1   = 'int32';
            $offset1 = $this->allocVar($ids[1], $type1, $line, $col);
            if ($offset1 !== null) {
                $this->emit("str x1, [x29, #-$offset1]",
                    "{$ids[1]} ← x1 (segundo retorno)");
                $this->func->setType($ids[1], $type1);
                $this->addSymbol($ids[1], $type1,
                    $this->func->name, null, $line, $col);
            }
        }

        // ids[2], ids[3], ... (más de dos retornos) quedan en x2, x3...
        // No generamos código extra porque el enunciado solo requiere hasta
        // dos valores de retorno para el alcance de este proyecto.
        if (count($ids) > 2) {
            $this->addError(
                'Semántico',
                'Multi-return con más de 2 valores no está soportado en esta versión',
                $line, $col
            );
        }

        return null;
    }

    // ── Helper de almacenamiento ──────────────────────────────────────────

    /**
     * Almacena x0 o s0 en el frame slot correspondiente según el tipo inferido.
     * Se reutiliza también en los casos normales A y B.
     * 
     * ✅ CORRECCIÓN: Usar w0 para int32/bool/rune, x0 para punteros/64-bit
     */
    private function storeInferredResult(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("str s0, [x29, #-$offset]", "guardar float32 inferido");
        } elseif (in_array($type, ['int32', 'bool', 'rune'])) {
            // ✅ Usar x0 (64-bit) para tipos enteros
            $this->emit("str x0, [x29, #-$offset]", "guardar $type inferido (64-bit)");
        } else {
            // Puntero, string, array → x0 (64-bit)
            $this->emit("str x0, [x29, #-$offset]", "guardar $type inferido (64-bit)");
        }
    }
}