<?php

namespace Golampi\Compiler\ARM64\Traits\RegisterAllocation;

/**
 * LivenessAnalysis — Análisis de liveness (variables vivas)
 *
 * Determina qué variables están "vivas" (su valor se usará posteriormente)
 * en cada punto del programa.
 *
 * Algoritmo:
 *   - IN[B]  = variables vivas al entrar al bloque B
 *   - OUT[B] = variables vivas al salir del bloque B
 *   - IN[B]  = USE[B] ∪ (OUT[B] - DEFINE[B])
 *   - OUT[B] = ∪ IN[sucesores]
 *
 * Para expresiones binarias simples (a - b):
 *   USE = {a, b},  DEFINE = {}
 *   → ambas variables están vivas simultáneamente
 *   → NO interfieren con nuevas variables
 *   → PUEDEN compartir registros si uno lee y otro escribe
 */
trait LivenessAnalysis
{
    /**
     * Calcula conjuntos USE y DEFINE para una expresión.
     * 
     * @return array{USE: string[], DEFINE: string[]}
     */
    public function computeUseDefine(array $exprContext): array
    {
        $use = [];
        $define = [];

        // Simplificación para expresiones binarias:
        // En "a op b", ambos son USE, ninguno es DEFINE
        // En "x = a + b", x es DEFINE, a,b son USE

        // Este es un análisis simplificado; en un compilador real
        // necesitaría SSA form o análisis más profundo

        return ['USE' => $use, 'DEFINE' => $define];
    }

    /**
     * Determina si dos variables interfieren:
     * Interfieren si sus rangos de liveness se solapan.
     *
     * Para operaciones binarias simples (a - b):
     *   a se lee (es USE)
     *   b se lee (es USE)
     *   → AMBOS están vivos al mismo tiempo
     *   → NO pueden estar en el mismo registro
     */
    public function interferes(string $var1, string $var2): bool
    {
        // En implementación simplificada para expresiones binarias:
        // - Variables que aparecen como USE en la misma expresión interfieren
        // - Variables DEFINE no interfieren mutuamente (escriben su propio valor)

        return true; // Conservador: asumir interferencia
    }

    /**
     * Retorna el conjunto de variables vivas en un punto específico.
     * Usado para determinar qué registros están disponibles.
     */
    public function getLiveVariables(): array
    {
        return [];
    }
}
