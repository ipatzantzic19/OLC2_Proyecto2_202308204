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
 * Valores por defecto (enunciado sección 3.2.3):
 *   int32   → 0       : mov x0, xzr + str x0
 *   float32 → 0.0     : movi d0, #0 + str s0
 *   bool    → false   : mov x0, xzr + str x0
 *   string  → ""      : adrp + add + str x0
 *   rune    → '\0'    : mov x0, xzr + str x0
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
     * OPTIMIZACIÓN: Si offset=0, no guardar (variable vive en registros).
     */
    protected function storeDefault(string $type, int $offset): void
    {
        if ($offset === 0) {
            // OPTIMIZACIÓN: Variable en registro, no guardar valor por defecto al stack
            // El valor por defecto ya está implícitamente en el registro
            return;
        }

        switch ($type) {
            case 'float32':
                // movi d0, #0 pone ceros en todos los bits de d0 (incluye s0)
                $this->emit('movi d0, #0',               'float32 default = 0.0');
                $this->emit("str s0, [x29, #-$offset]");
                break;

            case 'bool':
                $this->emit('mov x0, xzr',               'bool default = false');
                $this->emit("str x0, [x29, #-$offset]");
                break;

            case 'string':
                $empty = $this->internString('');
                $this->emit("adrp x0, $empty",           'string default = ""');
                $this->emit("add x0, x0, :lo12:$empty");
                $this->emit("str x0, [x29, #-$offset]");
                break;

            case 'rune':
                $this->emit('mov x0, xzr',               "rune default = '\\0'");
                $this->emit("str x0, [x29, #-$offset]");
                break;

            default: // int32, nil, pointer
                $this->emit('mov x0, xzr',               "$type default = 0");
                $this->emit("str x0, [x29, #-$offset]");
                break;
        }
    }
}