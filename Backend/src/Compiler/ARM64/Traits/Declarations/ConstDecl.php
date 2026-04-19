<?php

namespace Golampi\Compiler\ARM64\Traits\Declarations;

/**
 * ConstDeclTrait — Generación ARM64 para declaraciones de constantes
 *
 * Soporta la sintaxis:  const x T = expr
 *
 * Las constantes en Golampi son valores inmutables determinados en tiempo
 * de compilación. A nivel de generación de código ARM64, una constante se
 * trata como una variable local de solo lectura:
 *   - Se reserva un slot en el stack frame igual que una variable normal
 *   - Se inicializa con el valor de la expresión en la primera (y única) asignación
 *   - La inmutabilidad se valida en el análisis semántico (no aquí)
 *
 * Diferencia con variables:
 *   - La etiqueta en la tabla de símbolos incluye "(const)"
 *   - No puede usarse en el lado izquierdo de una asignación posterior
 *     (responsabilidad del SemanticAnalyzer / AssignmentsTrait)
 *
 * Nota de implementación:
 *   En ARM64 real, las constantes de tiempo de compilación se suelen
 *   colocar en la sección .rodata (read-only data). Para el alcance de
 *   este proyecto se almacenan en el stack frame con el mismo mecanismo
 *   que las variables, lo cual es correcto funcionalmente.
 */
trait ConstDecl
{
    public function visitConstDecl($ctx)
    {
        $name    = $ctx->ID()->getText();
        $typeCtx = $ctx->type();
        $type    = $this->getTypeName($typeCtx);
        $line    = $ctx->getStart()->getLine();
        $col     = $ctx->getStart()->getCharPositionInLine();

        $offset = $this->allocVar($name, $type, $line, $col);
        if ($offset === null) return null;

        $this->comment("const $name $type = expr");

        // Evaluar la expresión de inicialización
        $exprType = $this->visit($ctx->expression());

        // Aplicar conversión de tipo si es necesaria
        $this->applyConstCoercion($type, $exprType ?? $type);

        // Almacenar en el frame según el tipo
        if ($type === 'float32') {
            $this->emit("str s0, [x29, #-$offset]", "const $name (float32)");
        } else {
            $this->emit("str x0, [x29, #-$offset]", "const $name ($type)");
        }

        // Registrar en la tabla de símbolos con etiqueta "(const)"
        // Agregar como constante con el valor None (se usa el por defecto)
        $this->addSymbol($name, $type, $this->func->name, null, $line, $col, true);
        return null;
    }

    // ── Helper ────────────────────────────────────────────────────────────

    /**
     * Aplica coerción de tipo para el valor de la constante.
     * Misma lógica que VarDeclTrait pero separada para claridad.
     */
    private function applyConstCoercion(string $declaredType, string $exprType): void
    {
        if ($declaredType === $exprType) return;

        if ($declaredType === 'float32' && in_array($exprType, ['int32', 'rune'])) {
            $this->emitIntToFloat();
        } elseif ($declaredType === 'int32' && $exprType === 'float32') {
            $this->emitFloatToInt();
        }
        // Otros tipos: sin conversión implícita
    }
}