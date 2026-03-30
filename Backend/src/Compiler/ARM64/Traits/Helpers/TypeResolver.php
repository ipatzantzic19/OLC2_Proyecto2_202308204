<?php

namespace Golampi\Compiler\ARM64\Traits\Helpers;

/**
 * TypeResolver — Resolución de tipos del AST ANTLR4 a tipos internos del compilador
 *
 * Implementa la traducción desde los nodos de tipo del árbol sintáctico
 * generado por ANTLR4 hacia los identificadores de tipo internos del compilador.
 *
 * En la teoría de compiladores (Aho et al. — "Compiladores: Principios, Técnicas
 * y Herramientas"), esta fase corresponde al sistema de tipos y los "type records":
 * estructuras que almacenan la representación interna de cada tipo y permiten la
 * verificación estática de tipos en tiempo de compilación.
 *
 * El TypeResolver actúa como la "tabla de tipos" de la fase semántica:
 *   - Recibe un nodo de tipo del CST (Concrete Syntax Tree)
 *   - Devuelve el identificador canónico del tipo en el compilador
 *   - Garantiza que todos los componentes usen la misma representación interna
 *
 * Tipos soportados (enunciado Proyecto 2 — sección 3.2.3):
 *   Int32TypeContext    → 'int32'    (entero con signo, 32 bits)
 *   Float32TypeContext  → 'float32'  (punto flotante IEEE-754, 32 bits)
 *   BoolTypeContext     → 'bool'     (lógico: true/false)
 *   RuneTypeContext     → 'rune'     (alias de int32 para Unicode)
 *   StringTypeContext   → 'string'   (cadena de texto)
 *   ArrayTypeContext    → 'array'    (arreglo de tamaño fijo)
 *   SliceTypeContext    → 'slice'    (arreglo de tamaño variable)
 *   PointerTypeContext  → 'pointer'  (referencia a otro tipo)
 *
 * Estrategia de resolución (dos niveles):
 *   1. Por nombre de clase del contexto ANTLR4 (match exacto, O(1))
 *   2. Por texto del nodo (fallback para tipos compuestos como [N]T, *T)
 */
trait TypeResolver
{
    /**
     * Resuelve un contexto de tipo ANTLR4 al nombre de tipo interno del compilador.
     *
     * Es el único punto de entrada para resolución de tipos en el generador.
     * Garantiza que DeclarationsTrait, ExpressionsTrait, etc., usen
     * representaciones de tipo consistentes.
     *
     * @param  mixed  $typeCtx  Nodo de tipo del AST (puede ser null)
     * @return string           Nombre canónico del tipo: 'int32', 'float32', etc.
     */
    protected function getTypeName($typeCtx): string
    {
        if ($typeCtx === null) {
            return 'int32'; // tipo por defecto según el enunciado
        }

        // ── Resolución por nombre de clase (primer intento, más rápido) ────────
        $class = get_class($typeCtx);
        $base  = substr($class, strrpos($class, '\\') + 1);

        $byClass = match ($base) {
            'Int32TypeContext'   => 'int32',
            'Float32TypeContext' => 'float32',
            'BoolTypeContext'    => 'bool',
            'RuneTypeContext'    => 'rune',
            'StringTypeContext'  => 'string',
            'ArrayTypeContext'   => 'array',
            'SliceTypeContext'   => 'slice',
            'PointerTypeContext' => 'pointer',
            default             => null,
        };

        if ($byClass !== null) {
            return $byClass;
        }

        // ── Resolución por texto del nodo (fallback para tipos compuestos) ─────
        return $this->resolveTypeByText($typeCtx);
    }

    /**
     * Resuelve el tipo a partir del texto del nodo del AST.
     *
     * Se activa cuando el nombre de clase no coincide con los tipos primitivos,
     * para manejar tipos compuestos como [N]int32 (array) o *int32 (pointer).
     *
     * Análisis del texto:
     *   - Comienza con '[' → array o slice
     *   - Comienza con '*' → puntero
     *   - Texto exacto     → tipo primitivo
     *
     * @param  mixed  $typeCtx  Nodo de tipo cuyo texto se analizará
     * @return string           Nombre del tipo resuelto
     */
    private function resolveTypeByText($typeCtx): string
    {
        try {
            $text = trim($typeCtx->getText());

            // Tipos primitivos exactos
            $exact = match ($text) {
                'int32'   => 'int32',
                'float32' => 'float32',
                'bool'    => 'bool',
                'rune'    => 'rune',
                'string'  => 'string',
                default   => null,
            };

            if ($exact !== null) {
                return $exact;
            }

            // Tipos compuestos inferidos por prefijo
            if (str_starts_with($text, '[')) return 'array';
            if (str_starts_with($text, '*')) return 'pointer';

        } catch (\Throwable $e) {
            // En caso de error, retornar tipo por defecto
        }

        return 'int32'; // tipo por defecto seguro
    }
}