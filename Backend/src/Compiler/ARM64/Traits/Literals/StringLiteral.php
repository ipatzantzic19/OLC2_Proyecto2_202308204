<?php

namespace Golampi\Compiler\ARM64\Traits\Literals;

/**
 * StringLiteral — Generación ARM64 para literales string
 *
 * Los strings en Golampi son punteros a C-strings (null-terminated) en
 * la sección .data. El literal se interna en el StringPool y se carga
 * la dirección del string en x0 mediante direccionamiento PC-relativo.
 *
 * Generación:
 *   "Hola"  →  .str_0: .string "Hola"  (en .data)
 *              adrp x0, .str_0
 *              add  x0, x0, :lo12:.str_0
 *
 * La instrucción add con :lo12: combina la página base (adrp) con el
 * offset dentro de la página para obtener la dirección completa del string.
 *
 * Procesamiento de secuencias de escape PHP:
 *   El texto del token incluye las comillas. Se quitan y se procesan
 *   las secuencias \n, \t, \r, \\, \" para que el string ARM64 tenga
 *   los bytes correctos.
 *
 * Deduplicación:
 *   Strings idénticos comparten el mismo label en .data (ver StringPool).
 */
trait StringLiteral
{
    public function visitStringLiteral($ctx)
    {
        $text  = $ctx->STRING()->getText();
        $inner = substr($text, 1, -1); // quitar comillas dobles

        // Procesar secuencias de escape
        $processed = str_replace(
            ['\\n',  '\\t',  '\\r',  '\\\\', '\\"',  '\\a',  '\\b',  '\\f',  '\\v'],
            ["\n",   "\t",   "\r",   '\\',   '"',    "\x07", "\x08", "\x0C", "\x0B"],
            $inner
        );

        // Capturar valor para la tabla de símbolos
        $this->lastLiteralValue = ['type' => 'string', 'value' => $processed];

        $label = $this->internString($processed);
        $this->comment('string literal → x0 (puntero)');
        $this->emit("adrp x0, $label");
        $this->emit("add x0, x0, :lo12:$label");
        return 'string';
    }
}