<?php

namespace Golampi\Compiler\ARM64\Traits\Declarations;

/**
 * PrescanTrait — Pre-escaneo del bloque para alocación de variables
 *
 * El prescan es una pasada previa sobre el árbol de un bloque de función
 * que registra TODAS las variables locales antes de generar código.
 * Esto permite calcular el tamaño correcto del stack frame desde el inicio,
 * evitando tener que ajustar sp dinámicamente por cada declaración.
 *
 * ¿Por qué es necesario? (concepto de compiladores)
 *   En ARM64 el prólogo de la función reserva espacio con:
 *     sub sp, sp, #FRAME_SIZE
 *   Este valor debe conocerse ANTES de generar cualquier instrucción del cuerpo.
 *   El prescan resuelve este problema haciendo una pasada de "recolección"
 *   sobre el AST antes de la pasada de "generación".
 *
 * Análogo al "primer pasaje" de los ensambladores de dos pasadas:
 *   Pasada 1 (prescan) → recolectar símbolos y calcular offsets
 *   Pasada 2 (visit)   → generar código usando los offsets ya conocidos
 *
 * Tipos reconocidos:
 *   - VarDeclSimpleContext   : var x T
 *   - VarDeclWithInitContext : var x T = expr
 *   - ShortVarDeclContext    : x := expr  (tipo inferido → 'unknown' hasta visit)
 *   - ConstDeclContext       : const x T = expr
 *
 * Bloques anidados (if, for, switch) se escanean recursivamente porque
 * todas las variables locales de la función comparten el mismo stack frame.
 */
trait Prescan
{
    // ── Punto de entrada ─────────────────────────────────────────────────

    /**
     * Escanea un bloque completo (entre llaves { }).
     * Itera sobre todos los hijos internos (excluye los tokens { y }).
     */
    protected function prescanBlock($blockCtx): void
    {
        for ($i = 1; $i < $blockCtx->getChildCount() - 1; $i++) {
            $child = $blockCtx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $this->prescanNode($child);
            }
        }
    }

    // ── Escáner de nodo ───────────────────────────────────────────────────

    /**
     * Analiza un nodo del AST y registra las variables que declara.
     * Para nodos de control de flujo, recursa en sus bloques internos.
     */
    protected function prescanNode($ctx): void
    {
        $class = get_class($ctx);

        // ── var x T ───────────────────────────────────────────────────────
        if (str_ends_with($class, 'VarDeclSimpleContext') ||
            str_ends_with($class, 'VarDeclWithInitContext')) {
            $type = $this->resolveTypeFromCtx($ctx);
            
            // Verificar si es un tipo array: [N]T o [N][M]T, etc.
            $arrayDims = $this->extractArrayDimensions($ctx->type());
            
            if (!empty($arrayDims)) {
                // Es un array: registrar cada ID como array con las dimensiones
                $elemType = $this->extractArrayElementType($ctx->type());
                for ($i = 0; $i < $ctx->idList()->getChildCount(); $i += 2) {
                    $name = $ctx->idList()->getChild($i)->getText();
                    $this->prescanArrayVar($name, $arrayDims, $elemType);
                }
            } else {
                // Es una variable escalar: usar prescanIdList como antes
                $this->prescanIdList($ctx->idList(), $type);
            }
            return;
        }

        // ── x := expr ─────────────────────────────────────────────────────
        if (str_ends_with($class, 'ShortVarDeclContext')) {
            // El tipo se infiere al visitar la expresión; aquí usamos 'unknown'
            // para reservar el slot. El tipo se actualiza en visitShortVarDecl.
            $this->prescanIdList($ctx->idList(), 'unknown');
            return;
        }

        // ── const x T = expr ──────────────────────────────────────────────
        if (str_ends_with($class, 'ConstDeclContext')) {
            $name = $ctx->ID()->getText();
            if ($this->func && !$this->func->hasLocal($name)) {
                $type = $ctx->type() ? $this->getTypeName($ctx->type()) : 'int32';
                $this->func->allocLocal($name, $type);
            }
            return;
        }

        // ── Recursión en bloques anidados ─────────────────────────────────
        // Necesario para escanear variables dentro de if/for/switch
        for ($i = 0; $i < $ctx->getChildCount(); $i++) {
            $child = $ctx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $childClass = get_class($child);
                if (str_ends_with($childClass, 'BlockContext')) {
                    $this->prescanBlock($child);
                } else {
                    $this->prescanNode($child);
                }
            }
        }
    }

    // ── Helpers internos ──────────────────────────────────────────────────

    /**
     * Registra cada identificador de la lista en el FunctionContext.
     * Si ya existe (re-declaración en scope externo), lo ignora.
     * 
     * Para arrays, detecta el tipo ArrayType y registra con allocArray.
     */
    protected function prescanIdList($idList, string $type): void
    {
        if ($idList === null) return;

        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name = $idList->getChild($i)->getText();
            if ($this->func && !$this->func->hasLocal($name) && !$this->func->hasArray($name)) {
                // Verificar si es un array type por medio de examinar la estructura del tipoctx
                // Por ahora, registrar como variable escalar
                // (Fase 3: expandir para soportar ArrayType explícitamente)
                $this->func->allocLocal($name, $type);
            }
        }
    }

    /**
     * Registra un array multidimensional con todas sus dimensiones.
     * 
     * Ejemplo: var m [2][3]int32
     *          → calloc prescanArrayVar('m', [[2], [3]], 'int32')
     *
     * @param string $name        nombre del array (ej: "matrix")
     * @param array  $dims        lista de dimensiones [2, 3, ...] para [2][3]..., etc.
     * @param string $elemType    tipo de elemento ('int32', 'float32', 'bool', 'string', 'rune')
     */
    protected function prescanArrayVar(string $name, array $dims, string $elemType): void
    {
        if ($this->func && !$this->func->hasArray($name) && !$this->func->hasLocal($name)) {
            $this->func->allocArray($name, $dims, $elemType);
        }
    }

    /**
     * Resuelve el tipo de un contexto de declaración de variable.
     * Extrae el tipo del nodo de tipo del AST.
     */
    private function resolveTypeFromCtx($ctx): string
    {
        try {
            $typeCtx = $ctx->type();
            return $typeCtx ? $this->getTypeName($typeCtx) : 'int32';
        } catch (\Throwable $e) {
            return 'int32';
        }
    }

    /**
     * Extrae las dimensiones de un tipo array.
     * 
     * Ejemplo:
     *   [10]int32      → [10]
     *   [2][3]float32  → [2, 3]
     *   [5]int32       → [5]
     *
     * @param object $typeCtx Contexto de tipo ANTLR
     * @return array dimensiones extraídas, [] si no es array
     */
    private function extractArrayDimensions($typeCtx): array
    {
        if ($typeCtx === null) return [];

        $dims = [];
        try {
            $current = $typeCtx;
            $visits = 0;
            $maxVisits = 100;  // Prevenir loops infinitos

            // Recorrer la cadena de tipos mientras tengamos un tipo
            while ($current !== null && $visits < $maxVisits) {
                $visits++;
                $class = get_class($current);
                $base  = substr($class, strrpos($class, '\\') + 1);

                // Detectar ArrayType específicamente
                if ($base === 'ArrayTypeContext') {
                    // Extraer la expresión (dimensión)
                    $expr = null;
                    try {
                        if (is_callable([$current, 'expression'])) {
                            $expr = $current->expression();
                        }
                    } catch (\Throwable $e) {
                        // Si no tiene expression, saltar
                    }

                    if ($expr !== null) {
            // Es un ArrayType: extraer la dimensión
                        $dimValue = $this->evaluateLiteralExpression($expr);
                        if ($dimValue !== null && $dimValue > 0) {
                            $dims[] = $dimValue;

                            // Continuar con el tipo anidado
                            if (is_callable([$current, 'type'])) {
                                try {
                                    $current = $current->type();
                                } catch (\Throwable $e) {
                                    $current = null;
                                }
                            } else {
                                $current = null;
                            }
                        } else {
                            // Expresión no evaluable, terminar
                            break;
                        }
                    } else {
                        // No tiene expression, terminar
                        break;
                    }
                } else {
                    // No es ArrayType, terminar
                    break;
                }
            }
        } catch (\Throwable $e) {
            // Si hay error, asumir que no es array
            return [];
        }

        return $dims;
    }

    /**
     * Extrae el tipo de elemento base de un tipo array.
     * 
     * Ejemplo:
     *   [10]int32      → 'int32'
     *   [2][3]float32  → 'float32'
     *
     * @param object $typeCtx Contexto de tipo ANTLR
     * @return string tipo de elemento (o 'int32' por defecto)
     */
    private function extractArrayElementType($typeCtx): string
    {
        if ($typeCtx === null) return 'int32';

        try {
            $current = $typeCtx;

            // Navegar hasta el último tipo (base, no array)
            while ($current !== null) {
                $class = get_class($current);

                if (str_ends_with($class, 'ArrayTypeContext')) {
                    // Ir al tipo anidado
                    if (is_callable([$current, 'type'])) {
                        $current = $current->type();
                    } else {
                        break;
                    }
                } else {
                    // No es array, este es el tipo base
                    return $this->getTypeName($current);
                }
            }
        } catch (\Throwable $e) {}

        return 'int32';
    }

    /**
     * Intenta evaluar una expresión literal para obtener su valor numérico.
     * 
     * Soporta: INT32 literals
     * 
     * @param object $exprCtx Contexto de expresión ANTLR
     * @return int|null valor si es literal, null en otro caso
     */
    private function evaluateLiteralExpression($exprCtx): ?int
    {
        try {
            $class = get_class($exprCtx);

            // Si es una expresión simple que contiene un INT32
            if (str_ends_with($class, 'IntLiteralContext')) {
                return (int)$exprCtx->INT32()->getText();
            }

            // Si es un contexto de expresión que contiene un INT32 directo
            if (is_callable([$exprCtx, 'INT32'])) {
                $intToken = $exprCtx->INT32();
                if ($intToken !== null) {
                    return (int)$intToken->getText();
                }
            }
        } catch (\Throwable $e) {}

        return null;
    }
}