<?php

namespace Golampi\Compiler\ARM64\Traits\Literals;

/**
 * RuneLiteral — Generación ARM64 para literales rune
 *
 * El tipo rune en Golampi es un alias de int32 que representa un
 * código Unicode. Se escribe entre comillas simples: 'A', '\n', '€'
 *
 * Generación: igual que int32, el valor numérico Unicode se carga en x0.
 *
 *   'A'  → mov x0, #65
 *   '\n' → mov x0, #10
 *   '€'  → mov x0, #8364  (U+20AC)
 *
 * Secuencias de escape soportadas (enunciado + Go spec):
 *   \n \t \r \\ \' \0 \a \b \f \v
 *
 * mb_ord se usa para obtener el codepoint Unicode correcto de caracteres
 * multibyte, garantizando que '€' (3 bytes UTF-8) produzca 8364 y no 226.
 */
trait RuneLiteral
{
    public function visitRuneLiteral($ctx)
    {
        $text  = $ctx->RUNE()->getText();
        $inner = substr($text, 1, -1); // quitar comillas simples

        $val = match ($inner) {
            '\\n'  => 10,
            '\\t'  => 9,
            '\\r'  => 13,
            '\\\\' => 92,
            "\\'"  => 39,
            '\\0'  => 0,
            '\\a'  => 7,
            '\\b'  => 8,
            '\\f'  => 12,
            '\\v'  => 11,
            default => (mb_strlen($inner, 'UTF-8') === 1)
                        ? mb_ord($inner, 'UTF-8')
                        : ord($inner[0]),
        };

        $this->emit("mov x0, #$val", "rune '$inner' = U+$val");
        return 'rune';
    }
}