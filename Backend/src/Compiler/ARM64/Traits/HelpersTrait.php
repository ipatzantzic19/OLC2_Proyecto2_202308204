<?php

namespace Golampi\Compiler\ARM64\Traits;

use Golampi\Compiler\ARM64\FunctionContext;

/**
 * HelpersTrait
 *
 * Responsabilidad: utilidades de soporte usadas por múltiples traits:
 *   - Resolución de nombres de tipo
 *   - Alocación de variables en el frame
 *   - Valores por defecto
 *   - Tabla de símbolos
 *   - Reporte de errores
 *   - Visitors de identificadores y primarios simples
 */
trait HelpersTrait
{
    // ─── Tipos ───────────────────────────────────────────────────────────────

    /**
     * Obtiene el nombre de tipo normalizado a partir del contexto ANTLR.
     * Arrays y punteros son marcadores para Fases 3+.
     */
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

    /**
     * Reserva un slot en el frame de la función actual.
     * Devuelve el offset desde fp, o null si hay error.
     */
    protected function allocVar(string $name, string $type, int $line, int $col): ?int
    {
        if ($this->func === null) {
            $this->addError('Semántico', "Variable '$name' declarada fuera de función", $line, $col);
            return null;
        }
        if ($this->func->isFrameFull()) {
            $max = FunctionContext::MAX_FRAME / 8;
            $this->addError('Semántico',
                "Demasiadas variables en '{$this->func->name}' (máximo $max en Fase 1)",
                $line, $col);
            return null;
        }
        return $this->func->allocLocal($name, $type);
    }

    // ─── Valores por defecto ──────────────────────────────────────────────────

    /**
     * Emite las instrucciones ARM64 para almacenar el valor por defecto
     * del tipo dado en [fp - offset].
     */
    protected function storeDefault(string $type, int $offset): void
    {
        switch ($type) {
            case 'float32':
                $this->emit('movi d0, #0',              'float32 default = 0.0');
                $this->emit('fmov x0, d0');
                break;
            case 'bool':
                $this->emit('mov x0, #0',               'bool default = false');
                break;
            case 'string':
                $empty = $this->internString('');
                $this->emit("adrp x0, $empty",          'string default = ""');
                $this->emit("add x0, x0, :lo12:$empty");
                break;
            default:   // int32, rune, nil, pointer
                $this->emit('mov x0, #0',               "$type default = 0");
                break;
        }
        $this->emit("str x0, [x29, #-$offset]");
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
     * Carga una variable local al registro x0.
     * Si no existe, registra un error semántico.
     */
    public function visitIdentifier($ctx)
    {
        $name = $ctx->ID()->getText();
        $line = $ctx->getStart()->getLine();
        $col  = $ctx->getStart()->getCharPositionInLine();

        if (!$this->func || !$this->func->hasLocal($name)) {
            $this->addError('Semántico', "Variable '$name' no declarada", $line, $col);
            $this->emit('mov x0, #0');
            return 'int32';
        }

        $offset = $this->func->getOffset($name);
        $this->emit("ldr x0, [x29, #-$offset]",  "$name");
        return $this->func->getType($name);
    }
}