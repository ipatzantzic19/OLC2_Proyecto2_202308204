<?php

namespace Golampi\Compiler\ARM64\Traits\FunctionCall;

/**
 * PrintlnCall — Generación ARM64 para fmt.Println
 *
 * Implementa la función embebida fmt.Println que imprime uno o más valores
 * en la salida estándar separados por espacios, seguidos de un salto de línea.
 *
 * Implementación mediante printf de libc:
 *   fmt.Println(x)          → printf("%d\n", x)     para int32
 *   fmt.Println(x)          → printf("%g\n", d0)    para float32 (%g evita ceros innecesarios)
 *   fmt.Println(x)          → printf("%c\n", x)     para rune
 *   fmt.Println(x)          → printf("%s\n", x)     para string
 *   fmt.Println(x)          → printf("true\n") o printf("false\n") para bool
 *   fmt.Println(a, b, c)    → imprime cada valor con separador ' ' entre ellos
 *
 * Convención AArch64 para printf variadic:
 *   - x0 → puntero al format string
 *   - x1, x2, ... → argumentos enteros/punteros adicionales
 *   - d0 (float64) → argumentos float (ABI requiere double para variadic)
 *   Por eso float32 se convierte con fcvt d0, s0 antes de llamar a printf.
 */
trait PrintlnCall
{
    /**
     * Genera el código para fmt.Println con N argumentos.
     * Si no hay argumentos, imprime solo un salto de línea.
     */
    protected function generateFmtPrintln($argListCtx): void
    {
        if ($argListCtx === null) {
            $this->emitPrintNewline();
            return;
        }

        // Recopilar argumentos
        $argCtxList = [];
        for ($i = 0; $i < $argListCtx->getChildCount(); $i += 2) {
            $argCtxList[] = $argListCtx->getChild($i);
        }

        $n = count($argCtxList);
        $lastType = 'int32';

        for ($i = 0; $i < $n; $i++) {
            $isLast  = ($i === $n - 1);
            $argCtx  = $argCtxList[$i];
            $sep     = $isLast ? '' : ' ';  // separador entre valores

            // Obtener el nodo de expresión
            $exprCtx = null;
            try {
                if (is_callable([$argCtx, 'expression'])) {
                    $exprCtx = $argCtx->expression();
                }
            } catch (\Throwable $e) {}

            $type = $exprCtx ? ($this->visit($exprCtx) ?? 'int32') : 'int32';
            $this->comment("fmt.Println arg $i ($type)");
            $this->generatePrintValue($type, $sep);

            if ($isLast) {
                $lastType = $type;
            }
        }

        // BARE-METAL: Para int32, printInt ya escribe dígito + newline (2 bytes)
        // No hay que llamar emitPrintNewline adicional
        // Para otros tipos (string, bool, float), sí hay que escribir newline después
        if ($lastType !== 'int32') {
            $this->emitPrintNewline();
        }
    }

    /**
     * Genera printf para el valor actual en x0 o s0 según el tipo.
     *
     * @param string $type   Tipo del valor ('int32', 'float32', 'bool', etc.)
     * @param string $suffix Separador posterior ('' para el último, ' ' para los demás)
     */
    protected function generatePrintValue(string $type, string $suffix): void
    {
        match ($type) {
            'int32'   => $this->printInt($suffix),
            'float32' => $this->printFloat($suffix),
            'rune'    => $this->printRune($suffix),
            'bool'    => $this->printBool($suffix),
            default   => $this->printString($suffix),
        };
    }

    // ── Generadores por tipo ──────────────────────────────────────────────

    private function printInt(string $suffix): void
    {
        // ✅ CORRECCIÓN: Usar w-registers para int32 (32-bit)
        // Bare-metal: convertir int a ASCII (valor en w0 tras fmt.Println)
        $this->emit('add x3, x0, #48',              'convertir int a ASCII (x0 + 48 → x3)');
        $this->emit('adrp x4, buffer');
        $this->emit('add x4, x4, :lo12:buffer');
        $this->emit('strb w3, [x4]',                'guardar ASCII en buffer[0]');
        
        // Syscall write(1, buffer, 2)
        $this->emit('mov x0, #1',                   'fd = stdout');
        $this->emit('mov x1, x4',                   'buffer');
        $this->emit('mov x2, #2',                   'length = 2 (digit + newline)');
        $this->emit('mov x8, #64',                  'syscall write');
        $this->emit('svc #0',                       'invoke');
    }

    private function printFloat(string $suffix): void
    {
        // AArch64 variadic: float32 debe pasarse como float64 en d0
        $fmt = $this->internString('%g' . $suffix);
        $this->emitFloat32ToDouble();              // s0 → d0
        $this->emit("adrp x0, $fmt");
        $this->emit("add x0, x0, :lo12:$fmt");
        $this->emit('bl printf');
    }

    private function printRune(string $suffix): void
    {
        $fmt = $this->internString('%c' . $suffix);
        $this->emit('mov x1, x0',             'rune → x1 para printf %c');
        $this->emit("adrp x0, $fmt");
        $this->emit("add x0, x0, :lo12:$fmt");
        $this->emit('bl printf');
    }

    private function printBool(string $suffix): void
    {
        $trueLabel  = $this->newLabel('bool_true');
        $doneLabel  = $this->newLabel('bool_done');
        $falseStr   = $this->internString('false');
        $trueStr    = $this->internString('true');

        $this->emit("cbnz x0, $trueLabel",        'si true → imprimir "true"');
        
        // Print "false"
        $this->emit("adrp x0, $falseStr");
        $this->emit("add x0, x0, :lo12:$falseStr");
        $this->emit('mov x1, x0',           'buffer');
        $this->emit('mov x2, #5',           'length = strlen("false")');
        $this->emit('mov x0, #1',           'fd = stdout');
        $this->emit('mov x8, #64',          'syscall write');
        $this->emit('svc #0',               'invoke');
        $this->emit("b $doneLabel");
        
        $this->label($trueLabel);
        
        // Print "true"
        $this->emit("adrp x0, $trueStr");
        $this->emit("add x0, x0, :lo12:$trueStr");
        $this->emit('mov x1, x0',           'buffer');
        $this->emit('mov x2, #4',           'length = strlen("true")');
        $this->emit('mov x0, #1',           'fd = stdout');
        $this->emit('mov x8, #64',          'syscall write');
        $this->emit('svc #0',               'invoke');
        
        $this->label($doneLabel);
    }

    private function printString(string $suffix): void
    {
        $fmt = $this->internString('%s' . $suffix);
        $this->emit('mov x1, x0',             'ptr string → x1');
        $this->emit("adrp x0, $fmt");
        $this->emit("add x0, x0, :lo12:$fmt");
        $this->emit('bl printf');
    }

    private function emitPrintNewline(): void
    {
        // Bare-metal: newline via syscall
        $this->emit('adrp x4, msg');
        $this->emit('add x4, x4, :lo12:msg');
        $this->emit('mov x0, #1',                   'fd = stdout');
        $this->emit('mov x1, x4',                   'buffer = msg');
        $this->emit('mov x2, #1',                   'length = 1 (just newline)');
        $this->emit('mov x8, #64',                  'syscall write');
        $this->emit('svc #0',                       'invoke');
    }
}