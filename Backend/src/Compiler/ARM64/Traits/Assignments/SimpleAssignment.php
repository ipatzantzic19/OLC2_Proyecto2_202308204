<?php

namespace Golampi\Compiler\ARM64\Traits\Assignments;

/**
 * SimpleAssignment — Generación ARM64 para asignaciones escalares
 *
 * Soporta las formas:
 *   x  = expr
 *   x += expr   x -= expr   x *= expr   x /= expr
 *
 * Modelo de compiladores (Aho et al. — descriptores de dirección):
 *   Cada variable tiene un descriptor que indica su ubicación actual:
 *   un slot en el stack frame a offset [x29 - N]. El generador usa
 *   FunctionContext para resolver ese offset en tiempo de compilación.
 *
 * Operadores compuestos (x += expr equivale a x = x OP expr):
 *   1. Cargar lhs desde el frame al registro correcto (x0 o s0)
 *   2. Evaluar rhs → resultado en x0 o s0
 *   3. Combinar con la operación
 *   4. Almacenar resultado de vuelta en el frame
 *
 * Distinción float32 vs enteros:
 *   - float32 usa registros s0/s1 y operadores fadd/fsub/fmul/fdiv
 *   - enteros  usan registros x0/x1 y operadores add/sub/mul/sdiv
 *
 * Conversión automática en asignación simple (=):
 *   var x float32; x = 3    → int32 literal se convierte a float32 (scvtf)
 *   var x int32;   x = 3.0  → float32 se trunca a int32 (fcvtzs)
 */
trait SimpleAssignment
{
    public function visitSimpleAssignment($ctx)
    {
        $name = $ctx->ID()->getText();
        $op   = $ctx->assignOp()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            return null;
        }

        $offset  = $this->func->getOffset($name);
        $varType = $this->func->getType($name);
        $isFloat = ($varType === 'float32');

        if ($op === '=') {
            $this->comment("$name = expr");
            $exprType = $this->visit($ctx->expression());
            // Coerción automática si los tipos no coinciden
            $this->applyAssignCoercion($varType, $exprType ?? $varType);
            $this->storeToFrame($varType, $offset);

        } else {
            // Operadores compuestos: cargar lhs, evaluar rhs, aplicar op, guardar
            $this->comment("$name $op expr");

            if ($isFloat) {
                $this->loadFromFrame('float32', $offset);  // s0 = lhs
                $this->pushFloatStack();                    // s0 → stack
                $rhsType = $this->visit($ctx->expression());
                if (in_array($rhsType, ['int32', 'rune'])) {
                    $this->emitIntToFloat(); // convertir rhs si es entero
                }
                $this->popFloatStack();                    // s1 = lhs, s0 = rhs
                $scalar = $this->compoundOpToScalar($op);
                $this->emitFloatBinaryOp($scalar);
                $this->storeToFrame('float32', $offset);

            } else {
                $this->loadFromFrame($varType, $offset);   // x0 = lhs
                $this->pushStack();
                $this->visit($ctx->expression());           // rhs → x0
                $this->emit('ldr x1, [sp]', 'lhs ← stack');
                $this->emit('add sp, sp, #16');
                $scalar = $this->compoundOpToScalar($op);
                $this->emitBinaryOp($scalar);
                $this->storeToFrame($varType, $offset);
            }
        }
        return null;
    }

    // ── Helpers internos ──────────────────────────────────────────────────

    /** ✅ CORRECCIÓN: Guarda x0 o s0 en el slot de la variable según su tipo.
     *  - float32 → str s0
     *  - int32/bool/rune → str w0 (32-bit) 
     *  - puntero/dirección → str x0 (64-bit)
     */
    protected function storeToFrame(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("str s0, [x29, #-$offset]", 'guardar float32');
        } elseif (in_array($type, ['int32', 'bool', 'rune'])) {
            // ✅ Usar w0 (32-bit) para tipos enteros
            $this->emit("str w0, [x29, #-$offset]", "guardar $type (32-bit)");
        } else {
            // Puntero, string, array → x0 (64-bit)
            $this->emit("str x0, [x29, #-$offset]", "guardar $type (64-bit)");
        }
    }

    /** ✅ CORRECCIÓN: Carga desde el slot de la variable al registro correcto.
     *  - float32 → ldr s0
     *  - int32/bool/rune → ldr w0 (32-bit)
     *  - puntero/dirección → ldr x0 (64-bit)
     */
    protected function loadFromFrame(string $type, int $offset): void
    {
        if ($type === 'float32') {
            $this->emit("ldr s0, [x29, #-$offset]", 'cargar float32');
        } elseif (in_array($type, ['int32', 'bool', 'rune'])) {
            // ✅ Usar w0 (32-bit) para tipos enteros
            $this->emit("ldr w0, [x29, #-$offset]", "cargar $type (32-bit)");
        } else {
            // Puntero, string, array → x0 (64-bit)
            $this->emit("ldr x0, [x29, #-$offset]", "cargar $type (64-bit)");
        }
    }

    /** Convierte operador compuesto a operador escalar para los emitters. */
    private function compoundOpToScalar(string $op): string
    {
        return match ($op) {
            '+=' => '+', '-=' => '-', '*=' => '*', '/=' => '/',
            default => '+',
        };
    }

    /** Aplica coerción de tipo en asignación simple (=). */
    private function applyAssignCoercion(string $varType, string $exprType): void
    {
        if ($varType === $exprType) return;
        if ($varType === 'float32' && in_array($exprType, ['int32', 'rune'])) {
            $this->emitIntToFloat();
        } elseif ($varType === 'int32' && $exprType === 'float32') {
            $this->emitFloatToInt();
        }
    }
}