<?php

namespace Golampi\Compiler\ARM64\Traits\FunctionCall;

/**
 * BuiltinCall — Generación ARM64 para funciones embebidas (built-in)
 *
 * Implementa las funciones embebidas del lenguaje Golampi que no requieren
 * declaración previa (enunciado sección 3.3.13):
 *
 *   len(s)          → longitud de string (bl strlen) o array (compile-time)
 *   substr(s, i, n) → subcadena usando golampi_substr helper
 *   now()           → fecha/hora actual usando golampi_now helper
 *   typeOf(x)       → string con el nombre del tipo (resuelto en compile-time)
 *
 * Estas funciones se resuelven directamente durante la generación de código
 * (análisis semántico + generación unificados en el Visitor).
 *
 * Implementación:
 *   - len(string) → delega en strlen de libc: bl strlen → resultado en x0
 *   - substr      → helper ARM64 golampi_substr (malloc + memcpy + null terminator)
 *   - now()       → helper ARM64 golampi_now (time + localtime + strftime)
 *   - typeOf()    → el tipo se conoce en compile-time; se genera un puntero
 *                   a un string literal en .data con el nombre del tipo
 */
trait BuiltinCall
{
    // ── len ──────────────────────────────────────────────────────────────

    protected function generateLen($argListCtx): string
    {
        if ($argListCtx === null) {
            $this->emit('mov x0, xzr', 'len() sin argumentos → 0');
            return 'int32';
        }

        $argCtx  = $argListCtx->getChild(0);
        $exprCtx = $this->extractExpr($argCtx);
        $type    = $this->visit($exprCtx ?? $argCtx) ?? 'string';

        if ($type === 'string') {
            $this->comment('len(string) → strlen');
            $this->emit('bl strlen', 'strlen(x0) → longitud en x0');
        } elseif ($type === 'array') {
            // Fase 3: el tamaño del array es conocido en compile-time
            $this->emit('mov x0, xzr', 'len(array) — Fase 3');
        } else {
            $this->emit('mov x0, xzr', "len($type) no soportado");
        }
        return 'int32';
    }

    // ── substr ────────────────────────────────────────────────────────────

    protected function generateSubstr($argListCtx): string
    {
        if ($argListCtx === null) {
            $this->emit('mov x0, xzr');
            return 'string';
        }

        $args = [];
        for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
            $args[] = $argListCtx->getChild($i);
        }

        if (count($args) < 3) {
            $this->addError('Semántico', 'substr requiere 3 argumentos (string, int32, int32)', 0, 0);
            $this->emit('mov x0, xzr');
            return 'string';
        }

        // Evaluar y apilar: s → stack, start → stack, length → x2
        $this->visit($this->extractExpr($args[0]) ?? $args[0]);  // s → x0
        $this->emit('sub sp, sp, #16');
        $this->emit('str x0, [sp]', 's → stack');

        $this->visit($this->extractExpr($args[1]) ?? $args[1]);  // start → x0
        $this->emit('sub sp, sp, #16');
        $this->emit('str x0, [sp]', 'start → stack');

        $this->visit($this->extractExpr($args[2]) ?? $args[2]);  // length → x0
        $this->emit('mov x2, x0', 'length → x2');

        $this->emit('ldr x1, [sp]',   'start ← stack → x1');
        $this->emit('add sp, sp, #16');
        $this->emit('ldr x0, [sp]',   's ← stack → x0');
        $this->emit('add sp, sp, #16');

        $this->emitSubstr();   // bl golampi_substr → resultado en x0
        return 'string';
    }

    // ── now ───────────────────────────────────────────────────────────────

    protected function generateNow(): string
    {
        $this->emitNow();      // bl golampi_now → resultado en x0
        return 'string';
    }

    // ── typeOf ────────────────────────────────────────────────────────────

    protected function generateTypeOf($argListCtx): string
    {
        if ($argListCtx === null) {
            $label = $this->internString('nil');
            $this->emit("adrp x0, $label");
            $this->emit("add x0, x0, :lo12:$label");
            return 'string';
        }

        $argCtx  = $argListCtx->getChild(0);
        $exprCtx = $this->extractExpr($argCtx);
        $type    = $this->visit($exprCtx ?? $argCtx) ?? 'int32';

        $this->emitTypeOf($type);   // genera puntero a string del tipo en x0
        return 'string';
    }

    // ── Helper ────────────────────────────────────────────────────────────

    private function extractExpr($argCtx)
    {
        try {
            if (is_callable([$argCtx, 'expression'])) {
                return $argCtx->expression();
            }
        } catch (\Throwable $e) {}
        return null;
    }
}