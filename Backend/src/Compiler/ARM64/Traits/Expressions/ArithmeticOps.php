<?php

namespace Golampi\Compiler\ARM64\Traits\Expressions;

/**
 * ArithmeticOpsTrait — Generación ARM64 para operadores aritméticos
 *
 * Implementa los operadores aritméticos binarios del lenguaje Golampi
 * con soporte completo de la tabla de promoción de tipos del enunciado
 * (sección 3.3.6):
 *
 * Tabla de promoción para +, -, * (resumen):
 *   int32  OP int32   → int32   (add/sub/mul/sdiv)
 *   int32  OP float32 → float32 (scvtf lhs + fadd/fsub/fmul/fdiv)
 *   float32 OP int32  → float32 (scvtf rhs + fadd/fsub/fmul/fdiv)
 *   float32 OP float32→ float32 (fadd/fsub/fmul/fdiv)
 *   rune   OP int32   → int32
 *   rune   OP rune    → int32
 *   string  + string  → string  (golampi_concat helper)
 *
 * Módulo (%): solo int32 y rune (no float32)
 *
 * Estrategia de evaluación con temporales (Aho et al.):
 *   Para operadores binarios de tipo t1 OP t2:
 *     1. eval(lhs) → resultado en x0/s0
 *     2. push al stack (pushStack o pushFloatStack)
 *     3. eval(rhs) → resultado en x0/s0
 *     4. pop lhs del stack → x1/s1
 *     5. emitir instrucción binaria → resultado en x0/s0
 *
 * La cadena de operadores (a + b + c) se procesa de izquierda a derecha:
 *   result = (a + b); result = (result + c)
 */
trait ArithmeticOps
{
    // ── Aditivo: + y - ───────────────────────────────────────────────────

    public function visitAdditive($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->multiplicative(0));
        }

        // Evaluar primer operando
        $type = $this->visit($ctx->multiplicative(0));
        $mIdx = 1;

        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $op      = $ctx->getChild($i)->getText();
            $type    = $this->emitAddSub($type, $op, $ctx->multiplicative($mIdx++));
        }

        return $type;
    }

    // ── Multiplicativo: *, /, % ──────────────────────────────────────────

    public function visitMultiplicative($ctx)
    {
        if ($ctx->getChildCount() === 1) {
            return $this->visit($ctx->unary(0));
        }

        $type = $this->visit($ctx->unary(0));
        $uIdx = 1;

        for ($i = 1; $i < $ctx->getChildCount(); $i += 2) {
            $op   = $ctx->getChild($i)->getText();
            $type = $this->emitMulDivMod($type, $op, $ctx->unary($uIdx++));
        }

        return $type;
    }

    // ── Generadores de operaciones ────────────────────────────────────────

    /**
     * Genera código para + o - según los tipos de los operandos.
     * Gestiona la promoción int32 → float32 y concatenación de strings.
     *
     * @return string Tipo del resultado
     */
    private function emitAddSub(string $lhsType, string $op, $rhsCtx): string
    {
        if ($lhsType === 'float32') {
            return $this->emitFloatBinaryExpr($op, $rhsCtx);
        }

        if ($lhsType === 'string' && $op === '+') {
            return $this->emitStringConcatenationExpr($rhsCtx);
        }

        // int32 / rune: verificar si rhs es float → promover
        $this->pushStack();
        $rhsType = $this->visit($rhsCtx);

        if ($rhsType === 'float32') {
            // lhs está en stack como bits enteros, recuperar y convertir
            $this->emit('ldr x1, [sp]');
            $this->emit('add sp, sp, #16');
            $this->emit('scvtf s1, w1', 'lhs int32 → float32');
            // s0 ya tiene rhs float
            $this->emitFloatBinaryOp($op);
            return 'float32';
        }

        $this->emit('ldr x1, [sp]', 'lhs ← stack');
        $this->emit('add sp, sp, #16');
        $intOp = ($op === '+') ? 'add x0, x1, x0' : 'sub x0, x1, x0';
        $this->emit($intOp);
        return 'int32';
    }

    /**
     * Genera código para *, / o % según los tipos de los operandos.
     *
     * @return string Tipo del resultado
     */
    private function emitMulDivMod(string $lhsType, string $op, $rhsCtx): string
    {
        if ($lhsType === 'float32') {
            return $this->emitFloatBinaryExpr($op, $rhsCtx);
        }

        $this->pushStack();
        $rhsType = $this->visit($rhsCtx);

        if ($rhsType === 'float32' && ($op === '*' || $op === '/')) {
            // Promover lhs → float
            $this->emit('ldr x1, [sp]');
            $this->emit('add sp, sp, #16');
            $this->emit('scvtf s1, w1', 'lhs int32 → float32');
            $this->emitFloatBinaryOp($op);
            return 'float32';
        }

        $this->emit('ldr x1, [sp]', 'lhs ← stack');
        $this->emit('add sp, sp, #16');

        match ($op) {
            '*' => $this->emit('mul x0, x1, x0'),
            '/' => $this->emit('sdiv x0, x1, x0'),
            '%' => $this->emitModulo(),
            default => null,
        };
        return 'int32';
    }

    /**
     * Módulo: a % b = a - (a / b) * b
     * Precondición: x1 = lhs, x0 = rhs
     * ARM64 no tiene instrucción mod directa; se calcula via sdiv + msub.
     */
    private function emitModulo(): void
    {
        $this->emit('sdiv x2, x1, x0',     'x2 = lhs / rhs (cociente)');
        $this->emit('msub x0, x2, x0, x1', 'x0 = lhs - cociente * rhs (resto)');
    }

    /**
     * Genera operación binaria float32 OPTIMIZADA sin spill innecesario.
     * 
     * Usa RegisterAllocator para asignar registros óptimos basado en
     * análisis de interferencia (AHU Cap. 8-9).
     * 
     * Precondición: lhs ya evaluado en s0
     * Postcondición: resultado en s0
     */
    private function emitFloatBinaryExpr(string $op, $rhsCtx): string
    {
        // Obtener asignación óptima de registros
        $allocation = $this->allocateRegisterPair('float32');
        $lhsReg = $allocation['lhs'];  // Típicamente s0
        $rhsReg = $allocation['rhs'];  // Típicamente s1

        // s0 ya contiene lhs (precondición)
        // Si lhsReg != s0, mover resulta
        if ($lhsReg !== 's0') {
            $this->emit("fmov $lhsReg, s0", 'mover lhs a registro asignado');
        }

        // Evaluar rhs → s0
        $rhsType = $this->visit($rhsCtx);

        // Si rhs necesita conversión int32 → float32
        if ($rhsType === 'int32' || $rhsType === 'rune') {
            $this->emitIntToFloat();  // Convierte s0 en float32
        }

        // Si rhsReg != s0, mover resultado
        if ($rhsReg !== 's0') {
            $this->emit("fmov $rhsReg, s0", 'mover rhs a registro asignado');
        }

        // Emitir operación binaria SIN spill
        // Nota: emitFloatBinaryOp usa s0, s1 por defecto,
        // pero como asignamos optimalmente, evitamos push/pop
        match ($op) {
            '+'  => $this->emit("fadd $lhsReg, $lhsReg, $rhsReg", 'float32 suma optimizada'),
            '-'  => $this->emit("fsub $lhsReg, $lhsReg, $rhsReg", 'float32 resta optimizada'),
            '*'  => $this->emit("fmul $lhsReg, $lhsReg, $rhsReg", 'float32 multiplicación optimizada'),
            '/'  => $this->emit("fdiv $lhsReg, $lhsReg, $rhsReg", 'float32 división optimizada'),
            default => null,
        };

        // Resultado en lhsReg (típicamente s0)
        // Si lhsReg != s0, mover resultado a s0 para consistencia
        if ($lhsReg !== 's0') {
            $this->emit("fmov s0, $lhsReg", 'mover resultado a s0');
        }

        return 'float32';
    }

    /**
     * Genera concatenación de strings con el helper golampi_concat.
     * Precondición: lhs (string ptr) ya evaluado.
     * Nota: Se llama desde visitAdditive cuando se detecta operación string + string.
     * El helper golampi_concat se garantiza a través de StringOpsHandler::ensureConcatHelper.
     */
    private function emitStringConcatenationExpr($rhsCtx): string
    {
        $this->pushStack();               // lhs ptr → stack
        $this->visit($rhsCtx);           // rhs → x0
        $this->emit('mov x1, x0',        'rhs string → x1');
        $this->emit('ldr x0, [sp]',      'lhs string ← stack → x0');
        $this->emit('add sp, sp, #16');
        // El helper golampi_concat está definido en StringOpsTrait
        $this->ensureConcatHelper();
        $this->emit('bl golampi_concat', 'concat(x0=lhs, x1=rhs) → x0');
        return 'string';
    }
}