<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * FunctionCallTrait — Fase 2
 *
 * Generación de código ARM64 para llamadas a funciones.
 *
 * Cambios Fase 2:
 *   - Multi-parámetro: hasta 8 args en x0–x7 (int) y s0–s7 (float)
 *   - fmt.Println float32: fcvt d0, s0 → printf("%f", d0)
 *   - fmt.Println rune: imprime el carácter con %c
 *   - fmt.Println multi-arg: separados por espacio
 *   - Builtins: len, substr, now, typeOf
 *   - Guardar/restaurar registros caller-saved antes de bl
 *
 * Convención AArch64 (AAPCS64):
 *   x0–x7   → primeros 8 argumentos enteros/punteros
 *   s0–s7   → primeros 8 argumentos float (registros de punto flotante)
 *   x0      → valor de retorno entero/puntero
 *   s0      → valor de retorno float
 *   x0+x1   → retorno de hasta 128 bits (multi-return Golampi)
 */
trait FunctionCallTrait
{
    // ─── Visitor principal ────────────────────────────────────────────────────

    public function visitFunctionCall($ctx)
    {
        $ids  = $ctx->ID();
        $name = is_array($ids)
            ? ($ids[0]->getText() . (count($ids) >= 2 ? '.' . $ids[1]->getText() : ''))
            : $ids->getText();

        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        // ── Funciones built-in ──────────────────────────────────────────
        switch ($name) {
            case 'fmt.Println':
            case 'println':
                $this->generateFmtPrintln($ctx->argumentList());
                return 'nil';

            case 'len':
                return $this->generateLen($ctx->argumentList());

            case 'substr':
                return $this->generateSubstr($ctx->argumentList());

            case 'now':
                $this->emitNow();
                return 'string';

            case 'typeOf':
                return $this->generateTypeOf($ctx->argumentList());
        }

        // ── Función de usuario ─────────────────────────────────────────
        if (isset($this->userFunctions[$name])) {
            return $this->generateUserCall($name, $ctx->argumentList());
        }

        $this->addError('Semántico', "Función '$name' no definida", $line, $col);
        $this->emit('mov x0, xzr');
        return 'nil';
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  LLAMADA A FUNCIÓN DE USUARIO (multi-param)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Genera una llamada bl a una función de usuario.
     * Pasa hasta 8 argumentos enteros en x0–x7, floats en s0–s7.
     *
     * Protocolo:
     *   1. Guardar registros en uso (caller-saved: x9–x15, s8–s15) en stack
     *   2. Evaluar argumentos y cargar en x0–x7 / s0–s7
     *   3. bl nombre_funcion
     *   4. Restaurar registros
     *   5. Resultado en x0 (int) o s0 (float)
     */
    private function generateUserCall(string $name, $argListCtx): string
    {
        $args = [];
        if ($argListCtx) {
            for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
                $args[] = $argListCtx->getChild($i);
            }
        }

        $this->comment("llamada a $name (" . count($args) . " arg(s))");

        $argCount = count($args);

        if ($argCount === 0) {
            $this->emit("bl $name");
            return $this->inferReturnType($name);
        }

        // Evaluar cada argumento y guardarlo temporalmente en stack
        // Luego cargar en los registros correctos antes de bl
        $argTypes = [];

        // Fase 1: evaluar todos los argumentos y apilarlos
        for ($i = 0; $i < $argCount; $i++) {
            $argCtx = $args[$i];
            $exprNode = null;
            try {
                if (is_callable([$argCtx, 'expression'])) {
                    $exprNode = $argCtx->expression();
                }
            } catch (\Throwable $e) {}

            $type = $this->visit($exprNode ?? $argCtx) ?? 'int32';
            $argTypes[$i] = $type;

            // Guardar resultado en stack temporal
            if ($type === 'float32') {
                $this->emit('sub sp, sp, #16');
                $this->emit('str s0, [sp]',   "arg[$i] float32 → stack");
            } else {
                $this->emit('sub sp, sp, #16');
                $this->emit('str x0, [sp]',   "arg[$i] $type → stack");
            }
        }

        // Fase 2: cargar argumentos del stack en registros de parámetros
        // (en orden inverso, del último al primero)
        $intReg   = 0;  // contador de registros enteros (x0, x1, ...)
        $floatReg = 0;  // contador de registros float  (s0, s1, ...)

        // Calcular offsets correctos (primer arg está más profundo en stack)
        // Stack: [arg[n-1] en [sp]], [arg[n-2] en [sp+16]], ..., [arg[0] en [sp + (n-1)*16]]
        for ($i = $argCount - 1; $i >= 0; $i--) {
            $stackOffset = ($argCount - 1 - $i) * 16;
            $type = $argTypes[$i];

            if ($type === 'float32') {
                $reg = 's' . $floatReg++;
                $this->emit("ldr $reg, [sp, #$stackOffset]",  "cargar arg[$i] float → $reg");
            } else {
                $reg = 'x' . $intReg++;
                $this->emit("ldr $reg, [sp, #$stackOffset]",  "cargar arg[$i] $type → $reg");
            }
        }

        // Limpiar stack temporal
        $stackBytes = $argCount * 16;
        $this->emit("add sp, sp, #$stackBytes",  'limpiar args del stack');

        $this->emit("bl $name");
        return $this->inferReturnType($name);
    }

    /**
     * Infiere el tipo de retorno de una función de usuario.
     * En Fase 2: consulta la declaración registrada en $userFunctions.
     * Si no se puede determinar, asume 'int32'.
     */
    private function inferReturnType(string $name): string
    {
        if (!isset($this->userFunctions[$name])) return 'int32';
        $funcDecl = $this->userFunctions[$name];

        // Intentar obtener el tipo de retorno del nodo
        try {
            if (is_callable([$funcDecl, 'type'])) {
                $typeCtx = $funcDecl->type();
                if ($typeCtx !== null) {
                    return $this->getTypeName($typeCtx);
                }
            }
            // Multi-retorno
            if (is_callable([$funcDecl, 'typeList'])) {
                $typeList = $funcDecl->typeList();
                if ($typeList !== null) {
                    return 'multi';
                }
            }
        } catch (\Throwable $e) {}

        return 'int32';
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  ARGUMENTOS
    // ═══════════════════════════════════════════════════════════════════════════

    public function visitExpressionArgument($ctx)
    {
        return $this->visit($ctx->expression());
    }

    public function visitAddressArgument($ctx)
    {
        $name = $ctx->ID()->getText();
        if ($this->func && $this->func->hasLocal($name)) {
            $offset = $this->func->getOffset($name);
            $this->emit("sub x0, x29, #$offset",  "&$name");
        } else {
            $this->emit('mov x0, xzr');
        }
        return 'pointer';
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  fmt.Println — Fase 2 completo
    // ═══════════════════════════════════════════════════════════════════════════

    protected function generateFmtPrintln($argListCtx): void
    {
        if ($argListCtx === null) {
            $nlLabel = $this->internString("\n");
            $this->emit("adrp x0, $nlLabel");
            $this->emit("add x0, x0, :lo12:$nlLabel");
            $this->emit('bl printf');
            return;
        }

        $argCtxList = [];
        for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
            $argCtxList[] = $argListCtx->getChild($i);
        }

        $n = count($argCtxList);

        for ($i = 0; $i < $n; $i++) {
            $isLast = ($i === $n - 1);
            $argCtx = $argCtxList[$i];

            $exprCtx = null;
            try {
                if (is_callable([$argCtx, 'expression'])) {
                    $exprCtx = $argCtx->expression();
                }
            } catch (\Throwable $e) {}
            $exprCtx = $exprCtx ?? $argCtx;

            $type = $exprCtx ? ($this->visit($exprCtx) ?? 'int32') : 'int32';
            $sep  = $isLast ? '' : ' ';  // separador entre args

            $this->comment("fmt.Println arg $i ($type)");
            $this->generatePrintValue($type, $sep);
        }

        // Newline final
        $nlLabel = $this->internString("\n");
        $this->emit("adrp x0, $nlLabel");
        $this->emit("add x0, x0, :lo12:$nlLabel");
        $this->emit('bl printf');
    }

    /**
     * Genera printf para el valor actual (en x0 o s0) según tipo.
     *
     * @param string $type   tipo del valor
     * @param string $suffix separador posterior ('' o ' ')
     */
    protected function generatePrintValue(string $type, string $suffix): void
    {
        switch ($type) {
            case 'int32':
                $fmt = $this->internString('%d' . $suffix);
                $this->emit('mov x1, x0',               'int32 → x1 para printf');
                $this->emit("adrp x0, $fmt");
                $this->emit("add x0, x0, :lo12:$fmt");
                $this->emit('bl printf');
                break;

            case 'float32':
                // AArch64 variadic: los floats deben ir en d0 (float64)
                $fmt = $this->internString('%g' . $suffix);  // %g evita trailing zeros
                $this->emitFloat32ToDouble();                 // s0 → d0
                $this->emit("adrp x0, $fmt");
                $this->emit("add x0, x0, :lo12:$fmt");
                $this->emit('bl printf');
                break;

            case 'rune':
                // Rune: imprimir como carácter Unicode
                $fmt = $this->internString('%c' . $suffix);
                $this->emit('mov x1, x0',               'rune → x1 para printf %c');
                $this->emit("adrp x0, $fmt");
                $this->emit("add x0, x0, :lo12:$fmt");
                $this->emit('bl printf');
                break;

            case 'bool':
                $trueLabel  = $this->newLabel('bt');
                $doneLabel  = $this->newLabel('bd');
                $falseStr   = $this->internString('false' . $suffix);
                $trueStr    = $this->internString('true'  . $suffix);

                $this->emit("cbnz x0, $trueLabel");
                $this->emit("adrp x0, $falseStr");
                $this->emit("add x0, x0, :lo12:$falseStr");
                $this->emit('bl printf');
                $this->emit("b $doneLabel");
                $this->label($trueLabel);
                $this->emit("adrp x0, $trueStr");
                $this->emit("add x0, x0, :lo12:$trueStr");
                $this->emit('bl printf');
                $this->label($doneLabel);
                break;

            case 'string':
            default:
                $fmt = $this->internString('%s' . $suffix);
                $this->emit('mov x1, x0',               'ptr string → x1');
                $this->emit("adrp x0, $fmt");
                $this->emit("add x0, x0, :lo12:$fmt");
                $this->emit('bl printf');
                break;
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    //  BUILTINS ADICIONALES
    // ═══════════════════════════════════════════════════════════════════════════

    /** len(string) o len(array) */
    private function generateLen($argListCtx): string
    {
        if ($argListCtx === null) {
            $this->emit('mov x0, xzr');
            return 'int32';
        }

        $argCtx  = $argListCtx->getChild(0);
        $exprCtx = null;
        try {
            if (is_callable([$argCtx, 'expression'])) {
                $exprCtx = $argCtx->expression();
            }
        } catch (\Throwable $e) {}

        $type = $this->visit($exprCtx ?? $argCtx) ?? 'string';

        if ($type === 'string') {
            $this->emitStrlen();   // bl strlen → x0
        } elseif ($type === 'array') {
            // Array: tamaño conocido en compile time — Fase 3 lo completará
            $this->emit('mov x0, xzr',   'len(array) — Fase 3');
        } else {
            $this->emit('mov x0, xzr');
        }
        return 'int32';
    }

    /** substr(s, start, length) */
    private function generateSubstr($argListCtx): string
    {
        if ($argListCtx === null) {
            $this->emit('mov x0, xzr');
            return 'string';
        }

        // Evaluar los 3 argumentos
        $args = [];
        for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
            $args[] = $argListCtx->getChild($i);
        }

        if (count($args) < 3) {
            $this->addError('Semántico', "substr requiere 3 argumentos (string, int32, int32)", 0, 0);
            $this->emit('mov x0, xzr');
            return 'string';
        }

        // Evaluar s → x0, guardar
        $this->visitArgExpr($args[0]);
        $this->emit('sub sp, sp, #16');
        $this->emit('str x0, [sp]',   's → stack');

        // Evaluar start → x0, guardar
        $this->visitArgExpr($args[1]);
        $this->emit('sub sp, sp, #16');
        $this->emit('str x0, [sp]',   'start → stack');

        // Evaluar len → x2
        $this->visitArgExpr($args[2]);
        $this->emit('mov x2, x0',    'len → x2');

        // Recuperar start → x1
        $this->emit('ldr x1, [sp]',  'start ← stack');
        $this->emit('add sp, sp, #16');

        // Recuperar s → x0
        $this->emit('ldr x0, [sp]',  's ← stack');
        $this->emit('add sp, sp, #16');

        $this->emitSubstr();
        return 'string';
    }

    /** typeOf(expr) → string con nombre del tipo */
    private function generateTypeOf($argListCtx): string
    {
        if ($argListCtx === null) {
            $unknown = $this->internString('nil');
            $this->emit("adrp x0, $unknown");
            $this->emit("add x0, x0, :lo12:$unknown");
            return 'string';
        }

        $argCtx  = $argListCtx->getChild(0);
        $exprCtx = null;
        try {
            if (is_callable([$argCtx, 'expression'])) {
                $exprCtx = $argCtx->expression();
            }
        } catch (\Throwable $e) {}

        $type = $this->visit($exprCtx ?? $argCtx) ?? 'int32';
        $this->emitTypeOf($type);   // de StringOpsTrait
        return 'string';
    }

    /** Helper: visita un argumento y deja su resultado en x0/s0 */
    private function visitArgExpr($argCtx): string
    {
        $exprCtx = null;
        try {
            if (is_callable([$argCtx, 'expression'])) {
                $exprCtx = $argCtx->expression();
            }
        } catch (\Throwable $e) {}
        return $this->visit($exprCtx ?? $argCtx) ?? 'int32';
    }
}