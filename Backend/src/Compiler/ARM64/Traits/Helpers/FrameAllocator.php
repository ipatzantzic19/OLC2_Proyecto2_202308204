<?php

namespace Golampi\Compiler\ARM64\Traits\Helpers;

/**
 * FrameAllocator — Alocación de variables en el stack frame de la función
 *
 * Gestiona la asignación de slots de memoria en el registro de activación
 * de la función actual, delegando en FunctionContext.
 *
 * Modelo de compiladores (Aho et al. — registro de activación):
 *   Cada variable local ocupa 8 bytes alineados en el stack frame.
 *   El offset se calcula desde el frame pointer (x29):
 *     variable x con offset N → [x29 - N]
 *
 * También provee storeDefault: genera el código ARM64 que almacena
 * el valor por defecto del tipo cuando se declara una variable sin init.
 *
 * ✅ CORRECCIÓN FASE 3: Usar x-registers para int32/bool/rune (64-bit per AArch64 standard)
 * Valores por defecto (enunciado sección 3.2.3):
 *   int32   → 0       : mov x0, xzr + str x0  (64-bit)
 *   float32 → 0.0     : movi d0, #0 + str s0
 *   bool    → false   : mov x0, xzr + str x0  (64-bit)
 *   string  → ""      : adrp + add + str x0   (64-bit)
 *   rune    → '\0'    : mov x0, xzr + str x0  (64-bit)
 */
trait FrameAllocator
{
    /**
     * Registra una variable en el FunctionContext y devuelve su offset.
     * Retorna null si no hay función activa o el frame está lleno.
     */
    protected function allocVar(string $name, string $type, int $line, int $col): ?int
    {
        if ($this->func === null) {
            $this->addError('Semántico', "Variable '$name' declarada fuera de función", $line, $col);
            return null;
        }
        if ($this->func->isFrameFull()) {
            $this->addError(
                'Semántico',
                "Demasiadas variables en '{$this->func->name}' (límite de frame alcanzado)",
                $line, $col
            );
            return null;
        }
        return $this->func->allocLocal($name, $type);
    }

    /**
     * Genera el código que almacena el valor por defecto del tipo
     * en el slot [x29 - offset] del frame.
     * 
     * ✅ CORRECCIÓN: Usar x-registers para int32/bool/rune (64-bit per AArch64 standard)
     */
    protected function storeDefault(string $type, int $offset): void
    {
        switch ($type) {
            case 'float32':
                // movi d0, #0 pone ceros en todos los bits de d0 (incluye s0)
                $this->emit('movi d0, #0',               'float32 default = 0.0');
                $this->emit("str s0, [x29, #-$offset]");
                break;

            case 'bool':
                // ✅ Usar x0 (64-bit) para bool per AArch64 standard
                $this->emit('mov x0, xzr',               'bool default = false (64-bit)');
                $this->emit("str x0, [x29, #-$offset]");
                break;

            case 'string':
                // String es puntero → x0 (64-bit)
                $empty = $this->internString('');
                $this->emit("adrp x0, $empty",           'string default = ""');
                $this->emit("add x0, x0, :lo12:$empty");
                $this->emit("str x0, [x29, #-$offset]");
                break;

            case 'rune':
                // ✅ Usar x0 (64-bit) para rune per AArch64 standard
                $this->emit('mov x0, xzr',               "rune default = '\\0' (64-bit)");
                $this->emit("str x0, [x29, #-$offset]");
                break;

            default: // int32, nil, pointer
                // ✅ Usar x0 (64-bit) para int32 per AArch64 standard
                if (in_array($type, ['int32', 'nil'])) {
                    $this->emit('mov x0, xzr',               "$type default = 0 (64-bit)");
                    $this->emit("str x0, [x29, #-$offset]");
                } else {
                    // Puntero → x0 (64-bit)
                    $this->emit('mov x0, xzr',               "$type default = null (64-bit)");
                    $this->emit("str x0, [x29, #-$offset]");
                }
                break;
        }
    }
}