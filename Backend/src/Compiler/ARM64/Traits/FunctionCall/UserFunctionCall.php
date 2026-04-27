<?php

namespace Golampi\Compiler\ARM64\Traits\FunctionCall;

/**
 * UserFunctionCall — Generación ARM64 para llamadas a funciones de usuario
 *
 * Implementa la convención de llamadas AArch64 (AAPCS64) para funciones
 * definidas en el propio programa Golampi.
 *
 * Convención de paso de parámetros AArch64:
 *   x0–x7   → primeros 8 argumentos enteros/punteros/bool/string
 *   s0–s7   → primeros 8 argumentos float32
 *   x0      → valor de retorno entero/puntero
 *   s0      → valor de retorno float32
 *
 * Protocolo de llamada generado:
 *   1. Evaluar cada argumento → resultado en x0 o s0
 *   2. Apilar temporalmente en stack (para evaluación segura de todos)
 *   3. Cargar del stack a los registros de argumento (x0–x7 / s0–s7)
 *   4. bl nombre_funcion
 *   5. Resultado disponible en x0 (o s0 para float)
 *
 * Nota sobre el orden:
 *   Los argumentos se evalúan de izquierda a derecha y se apilan.
 *   Luego se cargan en orden inverso (del más profundo al más superficial)
 *   en los registros x0, x1, x2... para mantener el orden correcto.
 */
trait UserFunctionCall
{
    /**
     * Genera una llamada bl a una función de usuario.
     * Maneja hasta 8 argumentos enteros (x0–x7) y 8 float (s0–s7).
     */
    protected function generateUserCall(string $name, $argListCtx): string
    {
        $args = $this->collectArgs($argListCtx);
        $this->comment("llamada a $name (" . count($args) . " arg(s))");

        if (empty($args)) {
            $this->emit("bl $name");
            return $this->inferReturnType($name);
        }

        // ── Fase 1: evaluar todos los argumentos y apilar ─────────────────
        $argTypes = [];
        foreach ($args as $i => $argCtx) {
            $type         = $this->evalArg($argCtx) ?? 'int32';
            $argTypes[$i] = $type;

            if ($type === 'float32') {
                $this->emit('sub sp, sp, #16');
                $this->emit('str s0, [sp]', "arg[$i] float32 → stack");
            } else {
                $this->emit('sub sp, sp, #16');
                $this->emit('str x0, [sp]', "arg[$i] $type → stack");
            }
        }

        // ── Fase 2: cargar argumentos en registros de parámetros ──────────
        // Stack layout: arg[0] en [sp + (n-1)*16], arg[n-1] en [sp + 0]
        $intReg   = 0;
        $floatReg = 0;
        $n        = count($args);

        for ($i = 0; $i < $n; $i++) {
            $stackOffset = ($n - 1 - $i) * 16;
            $type        = $argTypes[$i];

            if ($type === 'float32') {
                $reg = 's' . $floatReg++;
                $this->emit("ldr $reg, [sp, #$stackOffset]", "arg[$i] float → $reg");
            } else {
                $reg = 'x' . $intReg++;
                $this->emit("ldr $reg, [sp, #$stackOffset]", "arg[$i] $type → $reg");
            }
        }

        // Limpiar stack de argumentos temporales
        $stackBytes = $n * 16;
        $this->emit("add sp, sp, #$stackBytes", 'limpiar args temporales del stack');
        $this->emit("bl $name");

        return $this->inferReturnType($name);
    }

    /**
     * Infiere el tipo de retorno consultando la declaración registrada.
     * Usa el nodo de tipo de la declaración ANTLR4 si está disponible.
     */
    protected function inferReturnType(string $name): string
    {
        if (!isset($this->userFunctions[$name])) return 'int32';

        $funcDecl = $this->userFunctions[$name];
        if (is_array($funcDecl) && isset($funcDecl['ctx'])) {
            $funcDecl = $funcDecl['ctx'];
        }

        if (!is_object($funcDecl)) {
            return 'int32';
        }

        try {
            if (is_callable([$funcDecl, 'type'])) {
                $typeCtx = $funcDecl->type();
                if ($typeCtx !== null) return $this->getTypeName($typeCtx);
            }
            if (is_callable([$funcDecl, 'typeList'])) {
                $typeList = $funcDecl->typeList();
                if ($typeList !== null) return 'multi';
            }
        } catch (\Throwable $e) {}

        return 'int32';
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /** Extrae la lista de argumentos de un argListCtx. */
    private function collectArgs($argListCtx): array
    {
        $args = [];
        if ($argListCtx === null) return $args;
        for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
            $args[] = $argListCtx->getChild($i);
        }
        return $args;
    }

    /** Evalúa un argumento individual y retorna su tipo. */
    private function evalArg($argCtx): string
    {
        $exprCtx = null;
        try {
            if (is_callable([$argCtx, 'expression'])) {
                $exprCtx = $argCtx->expression();
            }
        } catch (\Throwable $e) {}
        return $this->visit($exprCtx ?? $argCtx) ?? 'int32';
    }

    // ── Visitors de argumentos ────────────────────────────────────────────

    public function visitExpressionArgument($ctx)
    {
        return $this->visit($ctx->expression());
    }

    public function visitAddressArgument($ctx)
    {
        $name = $ctx->ID()->getText();
        if ($this->func && $this->func->hasLocal($name)) {
            $offset = $this->func->getOffset($name);
            $this->emit("sub x0, x29, #$offset", "&$name → dirección en frame");
        } else {
            $this->emit('mov x0, xzr', '&? → nil (variable no encontrada)');
        }
        return 'pointer';
    }
}