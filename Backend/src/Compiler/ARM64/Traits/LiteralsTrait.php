<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * LiteralsTrait
 *
 * Responsabilidad: generar código ARM64 para literales de todos los tipos
 * primitivos de Golampi: int32, float32, rune, string, bool, nil.
 *
 * Todos los visitors dejan el valor en x0 y devuelven el nombre del tipo.
 */
trait LiteralsTrait
{
    // ─── Entero ──────────────────────────────────────────────────────────────

    public function visitIntLiteral($ctx)
    {
        $val = (int) $ctx->INT32()->getText();

        // mov cubre 0‥65535; para valores mayores se usa movz + movk
        if ($val >= 0 && $val <= 65535) {
            $this->emit("mov x0, #$val");
        } else {
            $lo = $val & 0xFFFF;
            $this->emit("mov x0, #$lo");
            $hi = ($val >> 16) & 0xFFFF;
            if ($hi) $this->emit("movk x0, #$hi, lsl #16");
        }
        return 'int32';
    }

    // ─── Float ───────────────────────────────────────────────────────────────

    /**
     * Fase 1: almacena los bits IEEE-754 double (64-bit) en un registro entero.
     * Fase 2 usará registros SIMD (d0/s0) con fmov/fcvt.
     */
    public function visitFloatLiteral($ctx)
    {
        $val  = (float) $ctx->FLOAT32()->getText();
        $bits = unpack('Q', pack('d', $val))[1];

        $this->emit('mov x0, #' . ($bits & 0xFFFF));
        if ($bits > 0xFFFF)             $this->emit('movk x0, #' . (($bits >> 16) & 0xFFFF) . ', lsl #16');
        if ($bits > 0xFFFFFFFF)         $this->emit('movk x0, #' . (($bits >> 32) & 0xFFFF) . ', lsl #32');
        if ($bits > 0xFFFFFFFFFFFF)     $this->emit('movk x0, #' . (($bits >> 48) & 0xFFFF) . ', lsl #48');
        return 'float32';
    }

    // ─── Rune ────────────────────────────────────────────────────────────────

    public function visitRuneLiteral($ctx)
    {
        $text  = $ctx->RUNE()->getText();
        $inner = substr($text, 1, -1);   // quitar comillas simples

        $val = match ($inner) {
            '\\n'  => 10,  '\\t'  => 9,  '\\r' => 13,
            '\\\\'  => 92, "\\'"  => 39, '\\0' => 0,
            default => strlen($inner) === 1 ? ord($inner) : ord($inner[0]),
        };
        $this->emit("mov x0, #$val",  "'$inner' = $val");
        return 'rune';
    }

    // ─── String ──────────────────────────────────────────────────────────────

    public function visitStringLiteral($ctx)
    {
        $text  = $ctx->STRING()->getText();
        $inner = substr($text, 1, -1);

        // Procesar secuencias de escape a caracteres reales
        $processed = str_replace(
            ['\\n',  '\\t',  '\\r',  '\\\\', '\\"'],
            ["\n",   "\t",   "\r",   '\\',   '"'],
            $inner
        );

        $label = $this->internString($processed);
        $this->emit("adrp x0, $label");
        $this->emit("add x0, x0, :lo12:$label");
        return 'string';
    }

    // ─── Booleanos ───────────────────────────────────────────────────────────

    public function visitTrueLiteral($ctx)
    {
        $this->emit('mov x0, #1');
        return 'bool';
    }

    public function visitFalseLiteral($ctx)
    {
        $this->emit('mov x0, #0');
        return 'bool';
    }

    // ─── Nil ─────────────────────────────────────────────────────────────────

    public function visitNilLiteral($ctx)
    {
        $this->emit('mov x0, #0');
        return 'nil';
    }
}