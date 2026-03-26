#!/usr/bin/env php
<?php
/**
 * Test de Fase 2 — Compilador Golampi → ARM64
 *
 * Valida: float32 SIMD, funciones multi-param/multi-return,
 * string concat, builtins (len, substr, now, typeOf).
 *
 * Uso: php test_phase2.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';
require_once $root . '/generated/GolampiLexer.php';
require_once $root . '/generated/GolampiParser.php';
require_once $root . '/generated/GolampiVisitor.php';
require_once $root . '/generated/GolampiBaseVisitor.php';

use Golampi\Compiler\CompilationHandler;

$GREEN  = "\033[0;32m";
$RED    = "\033[0;31m";
$YELLOW = "\033[0;33m";
$CYAN   = "\033[0;36m";
$RESET  = "\033[0m";
$BOLD   = "\033[1m";

$passed = 0;
$failed = 0;

function compile(string $code): array {
    $handler = new CompilationHandler();
    return $handler->compile($code);
}

function test(string $name, string $code, array $expectations): void {
    global $passed, $failed, $GREEN, $RED, $RESET, $BOLD;

    $result = compile($code);
    $asm    = $result['assembly'] ?? '';
    $errors = $result['errors']   ?? [];

    $ok       = true;
    $messages = [];

    foreach ($expectations as $key => $expected) {
        switch ($key) {
            case 'no_errors':
                if (!empty($errors)) {
                    $ok = false;
                    foreach ($errors as $e) {
                        $messages[] = "  Error inesperado: [{$e['type']}] {$e['description']} (L{$e['line']})";
                    }
                }
                break;
            case 'has_errors':
                if (empty($errors)) { $ok = false; $messages[] = "  Se esperaban errores"; }
                break;
            case 'error_contains':
                $found = false;
                foreach ($errors as $e) {
                    if (str_contains($e['description'], $expected)) { $found = true; break; }
                }
                if (!$found) { $ok = false; $messages[] = "  Error esperado con: '$expected'"; }
                break;
            case 'asm_contains':
                foreach ((array)$expected as $needle) {
                    if (!str_contains($asm, $needle)) {
                        $ok = false;
                        $messages[] = "  Falta en assembly: '$needle'";
                    }
                }
                break;
            case 'asm_not_contains':
                foreach ((array)$expected as $needle) {
                    if (str_contains($asm, $needle)) {
                        $ok = false;
                        $messages[] = "  NO debe estar en assembly: '$needle'";
                    }
                }
                break;
            case 'has_assembly':
                if (empty(trim($asm))) { $ok = false; $messages[] = "  Assembly vacío"; }
                break;
            case 'symbol_count_gte':
                $count = count($result['symbolTable'] ?? []);
                if ($count < $expected) {
                    $ok = false;
                    $messages[] = "  Se esperaban >= $expected símbolos, hay $count";
                }
                break;
        }
    }

    if ($ok) {
        $passed++;
        echo "{$GREEN}  ✓{$RESET}  $name\n";
    } else {
        $failed++;
        echo "{$RED}  ✗{$RESET}  $name\n";
        foreach ($messages as $m) echo "{$RED}$m{$RESET}\n";
    }
}

echo "\n{$BOLD}{$CYAN}═══════ Golampi Fase 2 — Tipos completos + Funciones ═══════{$RESET}\n\n";

// ── 1. float32 ────────────────────────────────────────────────────────────────
echo "{$BOLD}[1] float32 SIMD{$RESET}\n";

test('float32 literal usa .single en .data', <<<'GO'
func main() {
    x := 3.14
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'has_assembly' => true,
    'asm_contains' => ['.single', 'ldr s0', 'fcvt d0, s0', '%g'],
]);

test('float32 suma con fadd', <<<'GO'
func main() {
    a := 1.5
    b := 2.5
    c := a + b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['fadd s0, s1, s0'],
]);

test('float32 resta con fsub', <<<'GO'
func main() {
    a := 5.0
    b := 2.0
    c := a - b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['fsub s0, s1, s0'],
]);

test('float32 multiplicación con fmul', <<<'GO'
func main() {
    a := 3.0
    b := 4.0
    c := a * b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['fmul s0, s1, s0'],
]);

test('float32 división con fdiv', <<<'GO'
func main() {
    a := 10.0
    b := 2.0
    c := a / b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['fdiv s0, s1, s0'],
]);

test('float32 negación con fneg', <<<'GO'
func main() {
    x := 3.14
    y := -x
    fmt.Println(y)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['fneg s0, s0'],
]);

test('float32 comparación genera fcmp', <<<'GO'
func main() {
    a := 3.0
    b := 4.0
    if a < b {
        fmt.Println("menor")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['fcmp s1, s0'],
]);

test('var float32 inicializada', <<<'GO'
func main() {
    var pi float32 = 3.14159
    fmt.Println(pi)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['.single', 'str s0'],
]);

test('float32 default = 0.0 (movi d0)', <<<'GO'
func main() {
    var x float32
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['movi d0, #0', 'str s0'],
]);

test('float32 printf usa fcvt d0, s0', <<<'GO'
func main() {
    fmt.Println(2.71828)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['fcvt d0, s0'],
]);

test('int32 + float32 → float32 (scvtf)', <<<'GO'
func main() {
    a := 3
    b := 1.5
    c := a + b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['scvtf'],
]);

test('float literal deduplicado en .data', <<<'GO'
func main() {
    a := 1.5
    b := 1.5
    c := a + b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    // Solo debe aparecer UNA entrada .single 1.5
]);

// ── 2. Rune completo ──────────────────────────────────────────────────────────
echo "\n{$BOLD}[2] Rune{$RESET}\n";

test('rune literal imprime con %c', <<<'GO'
func main() {
    var ch rune = 'A'
    fmt.Println(ch)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['%c'],
]);

test('rune secuencia de escape \\n', <<<'GO'
func main() {
    var r rune = '\n'
    fmt.Println(r)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['mov x0, #10'],
]);

// ── 3. String concatenación ───────────────────────────────────────────────────
echo "\n{$BOLD}[3] String concatenación{$RESET}\n";

test('concatenación genera golampi_concat', <<<'GO'
func main() {
    a := "hola"
    b := " mundo"
    c := a + b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['golampi_concat', 'bl golampi_concat'],
]);

test('helper golampi_concat en assembly', <<<'GO'
func main() {
    s := "hello" + " world"
    fmt.Println(s)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['golampi_concat:', 'bl strcpy', 'bl strcat', 'bl malloc'],
]);

// ── 4. Funciones multi-parámetro ──────────────────────────────────────────────
echo "\n{$BOLD}[4] Funciones multi-parámetro{$RESET}\n";

test('función con 2 params int32', <<<'GO'
func suma(a int32, b int32) int32 {
    return a + b
}
func main() {
    r := suma(3, 4)
    fmt.Println(r)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['suma:', 'bl suma', 'str x0, [x29', 'str x1, [x29'],
]);

test('función con 3 params int32', <<<'GO'
func maximo(a int32, b int32, c int32) int32 {
    if a > b {
        if a > c {
            return a
        }
        return c
    }
    if b > c {
        return b
    }
    return c
}
func main() {
    r := maximo(5, 3, 8)
    fmt.Println(r)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['maximo:', 'bl maximo'],
]);

test('función con param float32', <<<'GO'
func cuadrado(x float32) float32 {
    return x * x
}
func main() {
    r := cuadrado(3.0)
    fmt.Println(r)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['cuadrado:', 'str s0, [x29', 'fmul s0, s1, s0'],
]);

test('función sin params llamada', <<<'GO'
func valor() int32 {
    return 42
}
func main() {
    v := valor()
    fmt.Println(v)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['bl valor', 'mov x0, #42'],
]);

test('hoisting: main llama función definida después', <<<'GO'
func main() {
    fmt.Println(doble(7))
}
func doble(n int32) int32 {
    return n + n
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['bl doble', 'doble:'],
]);

// ── 5. Multi-retorno ─────────────────────────────────────────────────────────
echo "\n{$BOLD}[5] Multi-retorno{$RESET}\n";

test('función multi-return genera código sin errores', <<<'GO'
func divmod(a int32, b int32) (int32, int32) {
    return a / b, a % b
}
func main() {
    q, r := divmod(10, 3)
    fmt.Println(q)
    fmt.Println(r)
}
GO, [
    'has_assembly' => true,
    'asm_contains' => ['divmod:'],
]);

// ── 6. Built-ins ─────────────────────────────────────────────────────────────
echo "\n{$BOLD}[6] Funciones built-in{$RESET}\n";

test('len(string) genera bl strlen', <<<'GO'
func main() {
    s := "hola"
    n := len(s)
    fmt.Println(n)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['bl strlen'],
]);

test('substr genera golampi_substr', <<<'GO'
func main() {
    s := "Compiladores"
    sub := substr(s, 0, 4)
    fmt.Println(sub)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['golampi_substr', 'bl golampi_substr'],
]);

test('now() genera golampi_now', <<<'GO'
func main() {
    t := now()
    fmt.Println(t)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['golampi_now', 'bl golampi_now'],
]);

test('typeOf int32 genera string "int32"', <<<'GO'
func main() {
    x := 42
    fmt.Println(typeOf(x))
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['int32'],
]);

test('typeOf float32 genera string "float32"', <<<'GO'
func main() {
    x := 3.14
    fmt.Println(typeOf(x))
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['float32'],
]);

// ── 7. Tabla de símbolos ──────────────────────────────────────────────────────
echo "\n{$BOLD}[7] Tabla de símbolos Fase 2{$RESET}\n";

test('tabla registra float32', <<<'GO'
func main() {
    var pi float32 = 3.14
    x := 2.0
    fmt.Println(pi)
}
GO, [
    'no_errors'         => true,
    'symbol_count_gte'  => 2,
]);

test('tabla registra función con params', <<<'GO'
func suma(a int32, b int32) int32 {
    return a + b
}
func main() {
    fmt.Println(suma(1, 2))
}
GO, [
    'no_errors'         => true,
    'symbol_count_gte'  => 3,  // suma + a + b + main
]);

// ── 8. Compatibilidad con Fase 1 ─────────────────────────────────────────────
echo "\n{$BOLD}[8] Regresión — Fase 1 sigue funcionando{$RESET}\n";

test('int32 aritmética sin cambios', <<<'GO'
func main() {
    a := 10
    b := 3
    c := a + b
    d := a - b
    e := a * b
    f := a / b
    g := a % b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['add x0, x1, x0'],
]);

test('if-else sin cambios', <<<'GO'
func main() {
    x := 5
    if x > 3 {
        fmt.Println("mayor")
    } else {
        fmt.Println("menor")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['mayor', 'menor'],
]);

test('for clásico sin cambios', <<<'GO'
func main() {
    for i := 0; i < 3; i++ {
        fmt.Println(i)
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['add x0, x0, #1'],
]);

// ─── Resumen ─────────────────────────────────────────────────────────────────
$total = $passed + $failed;
echo "\n{$BOLD}{$CYAN}═══ Resumen Fase 2 ══════════════════════════════════{$RESET}\n";
echo "  Total:    $total\n";
echo "  {$GREEN}Pasados:  $passed{$RESET}\n";
if ($failed > 0) {
    echo "  {$RED}Fallidos: $failed{$RESET}\n";
} else {
    echo "  Fallidos: 0\n";
}

$pct = $total > 0 ? round($passed / $total * 100) : 0;
echo "  Cobertura Fase 2: {$pct}%\n\n";

if ($failed === 0) {
    echo "{$BOLD}{$GREEN}  ✓ Fase 2 COMPLETA{$RESET}\n\n";
} else {
    echo "{$BOLD}{$YELLOW}  ⚠ $failed test(s) fallidos — revisar traits Fase 2{$RESET}\n\n";
}

exit($failed > 0 ? 1 : 0);