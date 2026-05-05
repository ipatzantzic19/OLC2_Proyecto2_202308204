<?php

namespace Golampi\Compiler\ARM64\Traits\Expressions;

/**
 * ExpressionEntryTrait — Punto de entrada de la jerarquía de expresiones
 *
 * Provee el visitor de nivel más alto (visitExpression) que desencadena
 * el recorrido de la jerarquía de precedencia de operadores:
 *
 *   expression
 *     └── logicalOr           (||)         ← LogicalOpsTrait
 *           └── logicalAnd    (&&)         ← LogicalOpsTrait
 *                 └── equality  (==, !=)   ← ComparisonsTrait
 *                       └── relational (>,>=,<,<=) ← ComparisonsTrait
 *                             └── additive (+,-)   ← ArithmeticOpsTrait
 *                                   └── multiplicative (*,/,%) ← ArithmeticOpsTrait
 *                                         └── unary (-,!,&,*) ← UnaryOpsTrait
 *                                               └── primary   ← LiteralsTrait
 *                                                              + FunctionCallTrait
 *
 * Esta jerarquía implementa la precedencia de operadores del lenguaje Golampi
 * mediante la estructura gramatical (precedencia climbs en la gramática ANTLR4).
 * Es el equivalente directo de la sección 3.3.6–3.3.8 del enunciado.
 *
 * Separación de responsabilidades:
 *   Este trait contiene únicamente el punto de entrada y la documentación
 *   de la jerarquía. La lógica real está distribuida en los sub-traits
 *   especializados.
 */
trait ExpressionEntry
{
    /**
     * Visitador específico para expresiones ternarias
     * 
     * Gramática: expression : logicalOr '?' expression ':' expression # TernaryExpr
     * 
     * @return string Tipo del resultado
     */
    public function visitTernaryExpr($ctx)
    {
        $endLabel  = $this->newLabel('tern_end');
        $falseLabel = $this->newLabel('tern_false');

        // Evaluar condición (child 0: logicalOr)
        $this->visit($ctx->getChild(0));
        $this->emit("cbz x0, $falseLabel", 'ternario: si falso → rama else');

        // Rama verdadera (child 2: expression)
        $trueType = $this->visitChildren($ctx->getChild(2));
        $this->emit("b $endLabel", 'ternario: salta al final');

        // Rama falsa
        $this->label($falseLabel);
        $falseType = $this->visitChildren($ctx->getChild(4));

        $this->label($endLabel);
        return $trueType ?? $falseType ?? 'int32';
    }

    /**
     * Punto de entrada de toda evaluación de expresión.
     * Simplemente delega al primer nivel de la jerarquía (logicalOr).
     *
     * @return string Tipo del resultado ('int32', 'float32', 'bool', 'string', etc.)
     */
    public function visitExpression($ctx)
    {
        // No ternario: delegar a logicalOr
        return $this->visit($ctx->logicalOr());
    }
}