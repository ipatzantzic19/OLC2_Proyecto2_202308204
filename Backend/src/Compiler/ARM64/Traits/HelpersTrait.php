<?php

namespace Golampi\Compiler\ARM64\Traits;

use Golampi\Compiler\ARM64\FunctionContext;

/**
 * HelpersTrait — Fase 2
 *
 * Utilidades compartidas por múltiples traits:
 *   - Resolución de nombres de tipo (con soporte float32)
 *   - Alocación de variables en el frame (float en slots de 8 bytes)
 *   - Valores por defecto (float → 0.0 con fmov)
 *   - Tabla de símbolos y errores
 *   - visitIdentifier: distingue int vs float para emitir ldr correcto
 */
trait HelpersTrait
{
    // ─── Tipos ───────────────────────────────────────────────────────────────

    protected function getTypeName($typeCtx): string
    {
        if ($typeCtx === null) return 'int32';
        $text = $typeCtx->getText();
        if (str_starts_with($text, '[')) return 'array';
        if (str_starts_with($text, '*')) return 'pointer';
        return match ($text) {
            'int32'   => 'int32',
            'float32' => 'float32',
            'bool'    => 'bool',
            'string'  => 'string',
            'rune'    => 'rune',
            default   => 'int32',
        };
    }

    // ─── Alocación de variables ───────────────────────────────────────────────

    protected function allocVar(string $name, string $type, int $line, int $col): ?int
    {
        if ($this->func === null) {
            $this->addError('Semántico', "Variable '$name' declarada fuera de función", $line, $col);
            return null;
        }
        if ($this->func->isFrameFull()) {
            $this->addError('Semántico',
                "Demasiadas variables en '{$this->func->name}' (máximo en Fase 2)",
                $line, $col);
            return null;
        }
        return $this->func->allocLocal($name, $type);
    }

    // ─── Valores por defecto ──────────────────────────────────────────────────

    /**
     * Almacena el valor por defecto del tipo en [fp - offset].
     * Fase 2: float32 → fmov s0, #0.0 + str s0
     */
    protected function storeDefault(string $type, int $offset): void
    {
        switch ($type) {
            case 'float32':
                // 0.0 float: fmov s0, #0.0 no existe en ARM64 como inmediato.
                // Alternativa: movi d0, #0 (pone 0 en todos los bits de d0, incluye s0)
                $this->emit('movi d0, #0',              'float32 default = 0.0');
                $this->emit("str s0, [x29, #-$offset]");
                break;
            case 'bool':
                $this->emit('mov x0, xzr',              'bool default = false');
                $this->emit("str x0, [x29, #-$offset]");
                break;
            case 'string':
                $empty = $this->internString('');
                $this->emit("adrp x0, $empty",          'string default = ""');
                $this->emit("add x0, x0, :lo12:$empty");
                $this->emit("str x0, [x29, #-$offset]");
                break;
            case 'rune':
                $this->emit('mov x0, xzr',              "rune default = '\\0'");
                $this->emit("str x0, [x29, #-$offset]");
                break;
            default:   // int32, nil, pointer
                $this->emit('mov x0, xzr',              "$type default = 0");
                $this->emit("str x0, [x29, #-$offset]");
                break;
        }
    }

    // ─── Tabla de símbolos ────────────────────────────────────────────────────

    protected function addSymbol(
        string $id, string $type, string $scope,
        $value, int $line, int $col
    ): void {
        $this->symbolTable[] = [
            'identifier' => $id,
            'type'       => $type,
            'scope'      => $scope,
            'value'      => $value,
            'line'       => $line,
            'column'     => $col,
        ];
    }

    public function getSymbolTable(): array { return $this->symbolTable; }

    // ─── Errores ─────────────────────────────────────────────────────────────

    protected function addError(string $type, string $desc, int $line, int $col): void
    {
        $this->errors[] = [
            'type'        => $type,
            'description' => $desc,
            'line'        => $line,
            'column'      => $col,
        ];
    }

    public function getErrors(): array { return $this->errors; }

    // ─── Identificadores ─────────────────────────────────────────────────────

    /**
     * Carga una variable local al registro correspondiente según su tipo:
     *   - int32/bool/string/rune/pointer → x0
     *   - float32 → s0
     *
     * Devuelve el tipo de la variable.
     */
    public function visitIdentifier($ctx)
    {
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            $this->emit('mov x0, xzr');
            return 'int32';
        }

        $offset = $this->func->getOffset($name);
        $type   = $this->func->getType($name);

        if ($type === 'float32') {
            $this->emit("ldr s0, [x29, #-$offset]",  "$name (float32)");
        } else {
            $this->emit("ldr x0, [x29, #-$offset]",  "$name ($type)");
        }
        return $type;
    }
}