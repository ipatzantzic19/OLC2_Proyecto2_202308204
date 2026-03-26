<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * LiteralsTrait — Fase 2
 *
 * Genera código ARM64 para literales de todos los tipos primitivos.
 * Todos los visitors dejan el resultado en x0 (int) o s0 (float)
 * y devuelven el nombre del tipo como string PHP.
 *
 * Cambios Fase 2 respecto a Fase 1:
 *  - visitFloatLiteral: usa registros SIMD reales (s0) en lugar de movk de bits.
 *    Estrategia: valor flotante → constante en .data como .single → adrp+ldr s0
 *  - visitRuneLiteral: sigue siendo int32 (alias), sin cambios.
 *  - visitStringLiteral: sin cambios (sección .data + adrp/add).
 */
trait LiteralsTrait
{
    // ─── Entero ──────────────────────────────────────────────────────────────

    /**
     * Carga un literal int32 en x0.
     * mov cubre 0‥65535; para valores mayores usa movz + movk.
     * Para negativos usa movn o mov + neg.
     */
    public function visitIntLiteral($ctx)
    {
        $val = (int) $ctx->INT32()->getText();

        if ($val === 0) {
            $this->emit('mov x0, xzr',              'int32 literal 0');
        } elseif ($val >= 0 && $val <= 65535) {
            $this->emit("mov x0, #$val");
        } elseif ($val > 65535 && $val <= 0x7FFFFFFF) {
            $lo = $val & 0xFFFF;
            $hi = ($val >> 16) & 0xFFFF;
            $this->emit("mov x0, #$lo");
            if ($hi) $this->emit("movk x0, #$hi, lsl #16");
        } else {
            // Negativo: cargar como positivo y negar
            $abs = abs($val);
            $lo  = $abs & 0xFFFF;
            $hi  = ($abs >> 16) & 0xFFFF;
            $this->emit("mov x0, #$lo");
            if ($hi) $this->emit("movk x0, #$hi, lsl #16");
            if ($val < 0) $this->emit('neg x0, x0',       "literal negativo $val");
        }
        return 'int32';
    }

    // ─── Float ───────────────────────────────────────────────────────────────

    /**
     * Carga un literal float32 en s0 (registro SIMD).
     *
     * Estrategia correcta ARM64 Fase 2:
     *   1. Guardar el valor como .single en la sección .data
     *   2. Cargar con adrp + ldr s0, [x_page, :lo12:label]
     *
     * Esto es lo que hace GCC real para constantes flotantes.
     * El valor queda en s0; el generador sabe que el tipo es 'float32'.
     */
    public function visitFloatLiteral($ctx)
    {
        $text = $ctx->FLOAT32()->getText();
        $val  = (float) $text;

        // Intern the float constant in .data as a 4-byte .single
        $label = $this->internFloat($val);

        $this->comment("float32 literal $text → s0");
        $this->emit("adrp x9, $label",                  "página de constante float");
        $this->emit("ldr s0, [x9, :lo12:$label]",       "cargar float32 en s0");
        return 'float32';
    }

    // ─── Rune ────────────────────────────────────────────────────────────────

    /**
     * Rune es un alias de int32 en Golampi.
     * El valor numérico Unicode queda en x0.
     */
    public function visitRuneLiteral($ctx)
    {
        $text  = $ctx->RUNE()->getText();
        $inner = substr($text, 1, -1);  // quitar comillas simples

        $val = match ($inner) {
            '\\n'   => 10,
            '\\t'   => 9,
            '\\r'   => 13,
            '\\\\'  => 92,
            "\\'"   => 39,
            '\\0'   => 0,
            '\\a'   => 7,
            '\\b'   => 8,
            '\\f'   => 12,
            '\\v'   => 11,
            default => (mb_strlen($inner) === 1)
                         ? mb_ord($inner, 'UTF-8')
                         : ord($inner[0]),
        };

        $this->emit("mov x0, #$val",  "rune '$inner' = $val");
        return 'rune';
    }

    // ─── String ──────────────────────────────────────────────────────────────

    /**
     * Carga un puntero al string literal en x0.
     * El string se interna en el pool de la sección .data.
     */
    public function visitStringLiteral($ctx)
    {
        $text  = $ctx->STRING()->getText();
        $inner = substr($text, 1, -1);

        // Procesar secuencias de escape PHP
        $processed = str_replace(
            ['\\n',  '\\t',  '\\r',  '\\\\', '\\"',  '\\a', '\\b', '\\f', '\\v'],
            ["\n",   "\t",   "\r",   '\\',   '"',    "\x07","\x08","\x0C","\x0B"],
            $inner
        );

        $label = $this->internString($processed);
        $this->comment("string literal → x0");
        $this->emit("adrp x0, $label");
        $this->emit("add x0, x0, :lo12:$label");
        return 'string';
    }

    // ─── Booleanos ───────────────────────────────────────────────────────────

    public function visitTrueLiteral($ctx)
    {
        $this->emit('mov x0, #1',  'true');
        return 'bool';
    }

    public function visitFalseLiteral($ctx)
    {
        $this->emit('mov x0, xzr', 'false');
        return 'bool';
    }

    // ─── Nil ─────────────────────────────────────────────────────────────────

    public function visitNilLiteral($ctx)
    {
        $this->emit('mov x0, xzr', 'nil');
        return 'nil';
    }
}