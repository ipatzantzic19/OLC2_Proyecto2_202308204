<?php

namespace Golampi\Compiler\ARM64\Traits\Declarations;

/**
 * VarDeclTrait — Generación ARM64 para declaraciones de variable con tipo explícito
 *
 * Soporta las dos formas de declaración de variable con tipo:
 *
 *   var x T          → almacena el valor por defecto del tipo T
 *   var x T = expr   → evalúa expr y almacena el resultado
 *   var a, b T = e1, e2  → múltiples identificadores con la misma T
 *
 * Valores por defecto por tipo (conforme al enunciado):
 *   int32   → 0         (mov x0, xzr + str x0)
 *   float32 → 0.0       (movi d0, #0 + str s0)
 *   bool    → false     (mov x0, xzr + str x0)
 *   string  → ""        (adrp + add x0 + str x0)
 *   rune    → '\u0000'  (mov x0, xzr + str x0)
 *
 * Conversiones automáticas de tipo (tabla de promoción del enunciado):
 *   var x float32 = 3   → int32 literal se convierte a float32 via scvtf
 *   var x int32   = 3.0 → float32 literal se trunca a int32 via fcvtzs
 *
 * El slot de memoria ya fue reservado por el prescan (PrescanTrait).
 * Aquí solo se genera el código de inicialización.
 */
trait VarDecl
{
    // ── var x T ───────────────────────────────────────────────────────────

    public function visitVarDeclSimple($ctx)
    {
        $typeCtx = $ctx->type();
        $type    = $this->getTypeName($typeCtx);
        $idList  = $ctx->idList();
        $line    = $ctx->getStart()->getLine();
        $col     = $ctx->getStart()->getCharPositionInLine();

        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name   = $idList->getChild($i)->getText();
            
            // ── Verificar si es un array ────────────────────────────────────────
            if ($this->func && $this->func->hasArray($name)) {
                // Es un array: prescan ya lo registró, no generar código
                $arrayInfo = $this->func->getArrayInfo($name);
                if ($arrayInfo !== null) {
                    $this->comment("var $name [" . implode('][', $arrayInfo['dims']) . "]" . $arrayInfo['elem_type'] . " (ya alocado en prescan)");

                    // Inicialización por defecto del array a cero/nil.
                    $baseOffset = $arrayInfo['base_offset'] ?? 0;
                    $totalSlots = $arrayInfo['total_slots'] ?? 0;
                    for ($slot = 0; $slot < $totalSlots; $slot++) {
                        $offset = $baseOffset + ($slot * 8);
                        $this->emit('mov x0, xzr', "array default slot $slot = 0");
                        $this->emit("str x0, [x29, #-$offset]");
                    }

                    $this->addSymbol($name, 'array', $this->func->name, null, $line, $col);
                    // TODO: agregar información de dimensiones y tipo de elemento
                }
                continue;
            }

            // ── Variable escalar: inicializar ──────────────────────────────────
            $offset = $this->allocVar($name, $type, $line, $col);
            if ($offset === null) continue;

            $this->comment("var $name $type (valor por defecto)");
            $this->storeDefault($type, $offset);
            // El valor por defecto se calcula automáticamente en addSymbol
            $this->addSymbol($name, $type, $this->func->name, null, $line, $col);
        }
        return null;
    }

    // ── var x T = expr ────────────────────────────────────────────────────

    public function visitVarDeclWithInit($ctx)
    {
        $typeCtx  = $ctx->type();
        $type     = $this->getTypeName($typeCtx);
        $idList   = $ctx->idList();
        $exprList = $ctx->expressionList();
        $line     = $ctx->getStart()->getLine();
        $col      = $ctx->getStart()->getCharPositionInLine();

        // Contar expresiones de inicialización
        $exprCount = 0;
        for ($i = 0; $i < $exprList->getChildCount(); $i += 2) $exprCount++;

        $idx = 0;
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $name   = $idList->getChild($i)->getText();
            $offset = $this->allocVar($name, $type, $line, $col);
            if ($offset === null) { $idx++; continue; }

            if ($idx < $exprCount) {
                $exprCtx  = $exprList->getChild($idx * 2);
                $this->comment("var $name $type = expr");
                $exprType = $this->visit($exprCtx);

                // Conversión automática si los tipos difieren
                $finalType = $this->coerceIfNeeded($type, $exprType ?? $type);
                $this->storeResultTyped($type, $offset);
                $this->func->setType($name, $finalType);
            } else {
                // Más identificadores que expresiones → valor por defecto
                $this->storeDefault($type, $offset);
            }

            // El valor se pasará (null en inicialización con expresiones, por defecto se calcula)
            $this->addSymbol($name, $type, $this->func->name, null, $line, $col);
            $idx++;
        }
        return null;
    }

    // ── Helpers internos ──────────────────────────────────────────────────

    /**
     * Almacena el resultado de una expresión en el frame según el tipo.
     *   float32 → str s0, [x29, #-offset]
     *   demás   → str x0, [x29, #-offset]
     */
    private function storeResultTyped(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("str s0, [x29, #-$offset]", 'guardar float32');
        } else {
            $this->emit("str x0, [x29, #-$offset]", "guardar $type");
        }
    }

    /**
     * Aplica coerción de tipos automática cuando el tipo declarado
     * y el tipo de la expresión no coinciden.
     *
     * Tabla de conversiones (enunciado sección 3.3.6):
     *   int32  → float32 : scvtf s0, w0
     *   float32→ int32   : fcvtzs w0, s0 + sxtw
     */
    private function coerceIfNeeded(string $declaredType, string $exprType): string
    {
        if ($declaredType === $exprType) return $exprType;

        if ($declaredType === 'float32' && in_array($exprType, ['int32', 'rune'])) {
            $this->emitIntToFloat();
            return 'float32';
        }
        if ($declaredType === 'int32' && $exprType === 'float32') {
            $this->emitFloatToInt();
            return 'int32';
        }

        // Otros casos: sin conversión implícita (error semántico ignorado aquí)
        return $exprType;
    }
}