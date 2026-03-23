<?php

namespace Golampi\Compiler\ARM64\Traits;

/**
 * StringPoolTrait
 *
 * Responsabilidad: mantener un pool de cadenas constantes deduplicadas
 * y generar las entradas correspondientes en la sección .data.
 *
 * Estado que usa de la clase:
 *   array  $stringPool   (raw_value => label)
 *   int    $strIdx
 */
trait StringPoolTrait
{
    /**
     * Interna una cadena: si ya existe devuelve su label;
     * si no, genera un nuevo label, la añade a .data y la registra.
     */
    protected function internString(string $raw): string
    {
        if (!isset($this->stringPool[$raw])) {
            $label                   = '.str_' . $this->strIdx++;
            $this->stringPool[$raw]  = $label;
            $escaped                 = $this->asmEscape($raw);
            $this->addData($label . ': .string "' . $escaped . '"');
        }
        return $this->stringPool[$raw];
    }

    /**
     * Convierte una cadena PHP en sintaxis de cadena GNU as / clang as.
     * Escapa caracteres de control y comillas.
     */
    protected function asmEscape(string $s): string
    {
        $out = '';
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $c   = $s[$i];
            $ord = ord($c);
            if      ($c === '"')    { $out .= '\\"'; }
            elseif  ($c === '\\')  { $out .= '\\\\'; }
            elseif  ($c === "\n")  { $out .= '\\n'; }
            elseif  ($c === "\t")  { $out .= '\\t'; }
            elseif  ($c === "\r")  { $out .= '\\r'; }
            elseif  ($ord < 32 || $ord > 126) { $out .= sprintf('\\x%02x', $ord); }
            else    { $out .= $c; }
        }
        return $out;
    }
}