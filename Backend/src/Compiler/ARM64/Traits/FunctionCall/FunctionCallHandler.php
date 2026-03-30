<?php

namespace Golampi\Compiler\ARM64\Traits\FunctionCall;

use Golampi\Compiler\ARM64\Traits\FunctionCall\PrintlnCall;
use Golampi\Compiler\ARM64\Traits\FunctionCall\BuiltinCall;
use Golampi\Compiler\ARM64\Traits\FunctionCall\UserFunctionCall;

require_once __DIR__ . '/PrintlnCall.php';
require_once __DIR__ . '/BuiltinCall.php';
require_once __DIR__ . '/UserFunctionCall.php';

/**
 * FunctionCallHandler — Orquestador de generación de código para llamadas a funciones
 *
 * Punto central de despacho (dispatch) para todas las llamadas a función
 * en el lenguaje Golampi. Implementa el patrón Visitor para el nodo
 * FunctionCall del árbol sintáctico generado por ANTLR4.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  CLASIFICACIÓN DE LLAMADAS (Aho et al. — generación de código para funciones)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * El compilador distingue tres categorías de llamadas, cada una con su
 * propio mecanismo de generación de código ARM64:
 *
 *   1. Funciones de salida estándar (PrintlnCall):
 *      - fmt.Println / println → printf de libc
 *      - Soporta múltiples argumentos de distintos tipos
 *      - Tipo de retorno: nil (efecto secundario)
 *
 *   2. Funciones built-in del lenguaje (BuiltinCall):
 *      - len(s)          → bl strlen (para strings)
 *      - substr(s, i, n) → bl golampi_substr (helper ARM64)
 *      - now()           → bl golampi_now (helper ARM64)
 *      - typeOf(x)       → puntero a string constante en .data
 *      Estas funciones se resuelven DIRECTAMENTE durante la generación,
 *      sin necesidad de declaración previa en el código fuente.
 *
 *   3. Funciones de usuario (UserFunctionCall):
 *      - Funciones definidas con `func` en el programa Golampi
 *      - Convención AArch64 completa: x0–x7 (int), s0–s7 (float)
 *      - Soporta hoisting: pueden llamarse antes de su definición textual
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  CONVENCIÓN DE LLAMADAS AArch64 (AAPCS64)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   Parámetros enteros/punteros: x0–x7 (primeros 8)
 *   Parámetros float32:          s0–s7 (primeros 8)
 *   Valor de retorno entero:     x0
 *   Valor de retorno float32:    s0
 *   Valor de retorno múltiple:   x0 + x1 (hasta 128 bits)
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  DESPACHO (Patrón Strategy aplicado)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   visitFunctionCall() actúa como selector de estrategia:
 *     nombre → categoría → sub-trait → generación ARM64
 *
 *   El orden de resolución garantiza que los nombres built-in no puedan
 *   ser sobreescritos accidentalmente por funciones de usuario.
 *
 * Sub-traits y responsabilidades:
 *   PrintlnCall      → fmt.Println: tipos múltiples, separadores, newline
 *   BuiltinCall      → len, substr, now, typeOf
 *   UserFunctionCall → bl funcNombre con AArch64 calling convention
 */
trait FunctionCallHandler
{
    // ── Sub-traits especializados ─────────────────────────────────────────────
    use PrintlnCall;      // fmt.Println con soporte multi-tipo, multi-arg
    use BuiltinCall;      // len, substr, now, typeOf
    use UserFunctionCall; // funciones definidas por el usuario en Golampi

    // ═══════════════════════════════════════════════════════════════════════════
    //  VISITOR PRINCIPAL — DESPACHADOR DE LLAMADAS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Punto de entrada del Visitor para nodos FunctionCall del AST.
     *
     * Resuelve el nombre de la función y despacha a la implementación
     * correcta según la categoría:
     *
     *   1. Salida estándar (fmt.Println, println)
     *   2. Built-ins (len, substr, now, typeOf)
     *   3. Funciones de usuario registradas (hoisting ya realizado)
     *   4. Error semántico: función no definida
     *
     * @param  mixed  $ctx  Contexto FunctionCallContext del AST ANTLR4
     * @return string       Tipo del valor de retorno ('int32', 'float32', 'nil', etc.)
     */
    public function visitFunctionCall($ctx)
    {
        // ── Resolver nombre completo de la función ────────────────────────────
        // Soporta la notación de punto: fmt.Println (IDs separados por punto)
        $ids  = $ctx->ID();
        $name = is_array($ids)
            ? ($ids[0]->getText() . (count($ids) >= 2 ? '.' . $ids[1]->getText() : ''))
            : $ids->getText();

        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        // ── 1. Funciones de salida estándar ───────────────────────────────────
        // fmt.Println y println generan llamadas a printf de libc
        if ($name === 'fmt.Println' || $name === 'println') {
            $this->generateFmtPrintln($ctx->argumentList());
            return 'nil';  // Println no retorna valor (efecto secundario)
        }

        // ── 2. Funciones built-in del lenguaje Golampi ────────────────────────
        // Resueltas directamente en tiempo de compilación sin declaración previa
        switch ($name) {
            case 'len':
                return $this->generateLen($ctx->argumentList());

            case 'substr':
                return $this->generateSubstr($ctx->argumentList());

            case 'now':
                $this->emitNow();  // Delegado a NowHelper vía StringOpsHandler
                return 'string';

            case 'typeOf':
                return $this->generateTypeOf($ctx->argumentList());
        }

        // ── 3. Funciones de usuario (declaradas con func en Golampi) ──────────
        // Hoisting garantiza que estén registradas en $userFunctions
        if (isset($this->userFunctions[$name])) {
            return $this->generateUserCall($name, $ctx->argumentList());
        }

        // ── 4. Error semántico: función no definida ───────────────────────────
        $this->addError(
            'Semántico',
            "Función '$name' no definida. Verifica el nombre o la declaración.",
            $line,
            $col
        );
        $this->emit('mov x0, xzr', "función '$name' no encontrada → nil");
        return 'nil';
    }
}