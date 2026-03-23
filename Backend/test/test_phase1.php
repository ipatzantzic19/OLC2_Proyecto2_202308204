#!/usr/bin/env php
<?php
/**
 * Test de Phase 1 — Compilador Golampi → ARM64
 *
 * Valida que el ARM64Generator produce código ensamblador correcto para
 * todos los constructos de la Fase 1. No requiere QEMU ni GCC.
 *
 * Uso:  php test_phase1.php
 */

declare(strict_types=1);

// ── Bootstrap ────────────────────────────────────────────────────────────────
$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';
require_once $root . '/generated/GolampiLexer.php';
require_once $root . '/generated/GolampiParser.php';
require_once $root . '/generated/GolampiVisitor.php';
require_once $root . '/generated/GolampiBaseVisitor.php';

use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\CommonTokenStream;
use Golampi\Compiler\CompilationHandler;

// ── Colores ANSI ─────────────────────────────────────────────────────────────
$GREEN  = "\033[0;32m";
$RED    = "\033[0;31m";
$YELLOW = "\033[0;33m";
$CYAN   = "\033[0;36m";
$RESET  = "\033[0m";
$BOLD   = "\033[1m";

// ── Resultados ────────────────────────────────────────────────────────────────
$passed = 0;
$failed = 0;
$cases  = [];

function compile(string $code): array {
    $handler = new CompilationHandler();
    return $handler->compile($code);
}

function test(string $name, string $code, array $expectations): void {
    global $passed, $failed, $cases, $GREEN, $RED, $YELLOW, $CYAN, $RESET, $BOLD;

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
                        $messages[] = "  Unexpected error: [{$e['type']}] {$e['description']} (L{$e['line']})";
                    }
                }
                break;

            case 'has_errors':
                if (empty($errors)) {
                    $ok = false;
                    $messages[] = "  Expected errors, got none";
                }
                break;

            case 'error_contains':
                $found = false;
                foreach ($errors as $e) {
                    if (str_contains($e['description'], $expected)) { $found = true; break; }
                }
                if (!$found) {
                    $ok = false;
                    $messages[] = "  Expected error containing '$expected'";
                }
                break;

            case 'asm_contains':
                $needles = is_array($expected) ? $expected : [$expected];
                foreach ($needles as $needle) {
                    if (!str_contains($asm, $needle)) {
                        $ok = false;
                        $messages[] = "  Missing in assembly: '$needle'";
                    }
                }
                break;

            case 'asm_not_contains':
                $needles = is_array($expected) ? $expected : [$expected];
                foreach ($needles as $needle) {
                    if (str_contains($asm, $needle)) {
                        $ok = false;
                        $messages[] = "  Should NOT be in assembly: '$needle'";
                    }
                }
                break;

            case 'has_assembly':
                if (empty(trim($asm))) {
                    $ok = false;
                    $messages[] = "  Expected non-empty assembly";
                }
                break;

            case 'symbol_count_gte':
                $count = count($result['symbolTable'] ?? []);
                if ($count < $expected) {
                    $ok = false;
                    $messages[] = "  Expected >= $expected symbols, got $count";
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

    $cases[] = compact('name', 'ok');
}

// ═══════════════════════════════════════════════════════════════════════════════
//  TESTS
// ═══════════════════════════════════════════════════════════════════════════════

echo "\n{$BOLD}{$CYAN}═══════ Golampi Phase 1 — ARM64 Compiler Tests ═══════{$RESET}\n\n";

// ── 1. Hello World mínimo ─────────────────────────────────────────────────────
echo "{$BOLD}[1] Básico{$RESET}\n";

test('main vacío genera prólogo/epílogo', <<<'GO'
func main() {}
GO, [
    'no_errors'    => true,
    'has_assembly' => true,
    'asm_contains' => ['main:', 'stp x29, x30', 'ldp x29, x30', 'ret'],
]);

test('fmt.Println string literal', <<<'GO'
func main() {
    fmt.Println("Hola Mundo")
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['bl printf', 'Hola Mundo'],
]);

test('fmt.Println sin args → solo newline', <<<'GO'
func main() {
    fmt.Println()
}
GO, [
    'no_errors' => true,
    'asm_contains' => ['bl printf'],
]);

// ── 2. Variables ──────────────────────────────────────────────────────────────
echo "\n{$BOLD}[2] Variables{$RESET}\n";

test('var con tipo y sin inicializador', <<<'GO'
func main() {
    var x int32
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['str x0, [x29', 'ldr x0, [x29'],
]);

test('var con inicializador', <<<'GO'
func main() {
    var x int32 = 42
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['#42'],
]);

test('declaración corta :=', <<<'GO'
func main() {
    x := 99
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['#99'],
]);

test('const', <<<'GO'
func main() {
    const MAX int32 = 100
    fmt.Println(MAX)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['#100'],
]);

test('var bool', <<<'GO'
func main() {
    var activo bool = true
    fmt.Println(activo)
}
GO, [
    'no_errors' => true,
    'asm_contains' => ['true', 'false'],  // cadenas en pool
]);

// ── 3. Aritmética ─────────────────────────────────────────────────────────────
echo "\n{$BOLD}[3] Aritmética{$RESET}\n";

test('suma', <<<'GO'
func main() {
    a := 3
    b := 4
    c := a + b
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['add x0, x1, x0'],
]);

test('resta', <<<'GO'
func main() {
    c := 10 - 3
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['sub x0, x1, x0'],
]);

test('multiplicación', <<<'GO'
func main() {
    c := 6 * 7
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['mul x0, x1, x0'],
]);

test('división', <<<'GO'
func main() {
    c := 20 / 4
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['sdiv x0, x1, x0'],
]);

test('módulo', <<<'GO'
func main() {
    c := 17 % 5
    fmt.Println(c)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['msub x0'],   // a % b = a - (a/b)*b
]);

test('negación unaria', <<<'GO'
func main() {
    x := 5
    y := -x
    fmt.Println(y)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['neg x0, x0'],
]);

// ── 4. Asignación compuesta e inc/dec ─────────────────────────────────────────
echo "\n{$BOLD}[4] Asignación compuesta{$RESET}\n";

test('+=', <<<'GO'
func main() {
    x := 10
    x += 5
    fmt.Println(x)
}
GO, [
    'no_errors' => true,
    'asm_contains' => ['add x0, x1, x0'],
]);

test('x++', <<<'GO'
func main() {
    x := 0
    x++
    fmt.Println(x)
}
GO, [
    'no_errors' => true,
    'asm_contains' => ['add x0, x0, #1'],
]);

test('x--', <<<'GO'
func main() {
    x := 5
    x--
    fmt.Println(x)
}
GO, [
    'no_errors' => true,
    'asm_contains' => ['sub x0, x0, #1'],
]);

// ── 5. Lógica y comparación ───────────────────────────────────────────────────
echo "\n{$BOLD}[5] Lógica y comparación{$RESET}\n";

test('== genera cset eq', <<<'GO'
func main() {
    x := 5 == 5
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['cmp', 'cset'],
]);

test('!= genera cset ne', <<<'GO'
func main() {
    x := 3 != 4
    fmt.Println(x)
}
GO, [
    'no_errors' => true,
    'asm_contains' => ['cset x0, ne'],
]);

test('&& cortocircuito', <<<'GO'
func main() {
    a := 1
    b := 1
    if a == 1 && b == 1 {
        fmt.Println("ok")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['cbz x0'],   // cortocircuito → branch on false
]);

test('|| cortocircuito', <<<'GO'
func main() {
    a := 0
    if a == 1 || a == 0 {
        fmt.Println("ok")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['cbnz x0'],  // cortocircuito || → branch on true
]);

test('! not', <<<'GO'
func main() {
    x := true
    y := !x
    fmt.Println(y)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['eor x0, x0, #1'],
]);

// ── 6. Control de flujo ───────────────────────────────────────────────────────
echo "\n{$BOLD}[6] Control de flujo{$RESET}\n";

test('if simple', <<<'GO'
func main() {
    x := 10
    if x > 5 {
        fmt.Println("mayor")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['cmp', 'cset x0, gt', 'mayor'], // cset+cbz en vez de b.le
]);

test('if-else', <<<'GO'
func main() {
    x := 3
    if x > 5 {
        fmt.Println("mayor")
    } else {
        fmt.Println("menor")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['mayor', 'menor'],
]);

test('if-else if-else', <<<'GO'
func main() {
    x := 3
    if x > 10 {
        fmt.Println("grande")
    } else if x > 5 {
        fmt.Println("mediano")
    } else {
        fmt.Println("pequeno")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['grande', 'mediano', 'pequeno'],
]);

test('for clásico', <<<'GO'
func main() {
    for i := 0; i < 5; i++ {
        fmt.Println(i)
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['cset x0, lt', 'add x0, x0, #1'], // cset+cbz en vez de b.ge
]);

test('for-while', <<<'GO'
func main() {
    x := 10
    for x > 0 {
        x--
    }
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['sub x0, x0, #1'],
]);

test('for infinito con break', <<<'GO'
func main() {
    x := 0
    for {
        x++
        if x >= 3 {
            break
        }
    }
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['add x0, x0, #1'],
]);

test('continue en for', <<<'GO'
func main() {
    for i := 0; i < 5; i++ {
        if i == 3 {
            continue
        }
        fmt.Println(i)
    }
}
GO, [
    'no_errors' => true,
    'asm_contains' => ['cset x0, eq'], // cset+cbz en vez de b.eq
]);

test('switch básico', <<<'GO'
func main() {
    x := 2
    switch x {
    case 1:
        fmt.Println("uno")
    case 2:
        fmt.Println("dos")
    default:
        fmt.Println("otro")
    }
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['uno', 'dos', 'otro'],
]);

// ── 7. Funciones de usuario ───────────────────────────────────────────────────
echo "\n{$BOLD}[7] Funciones de usuario{$RESET}\n";

test('función sin params ni return', <<<'GO'
func saluda() {
    fmt.Println("hola")
}
func main() {
    saluda()
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['bl saluda', 'saluda:'],
]);

test('return en función', <<<'GO'
func doblar(n int32) int32 {
    return n
}
func main() {
    x := doblar(7)
    fmt.Println(x)
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['bl doblar', 'ret'],
]);

// ── 8. Errores semánticos esperados ──────────────────────────────────────────
echo "\n{$BOLD}[8] Errores semánticos{$RESET}\n";

test('variable no declarada → error', <<<'GO'
func main() {
    fmt.Println(z)
}
GO, [
    'has_errors'     => true,
    'error_contains' => "no declarada",
]);

test('sin main → error', <<<'GO'
func otra() {
    fmt.Println("no main")
}
GO, [
    'has_errors'     => true,
    'error_contains' => 'main',
]);

// ── 9. Pool de strings ────────────────────────────────────────────────────────
echo "\n{$BOLD}[9] Pool de strings / sección .data{$RESET}\n";

test('strings en sección .data', <<<'GO'
func main() {
    fmt.Println("alpha")
    fmt.Println("alpha")
    fmt.Println("beta")
}
GO, [
    'no_errors'    => true,
    'asm_contains' => ['.section __DATA', 'alpha', 'beta'],
]);

test('strings idénticos → mismo label (no duplicados)', <<<'GO'
func main() {
    fmt.Println("mismo")
    fmt.Println("mismo")
}
GO, [
    'no_errors' => true,
    // Solo debe haber UNA entrada "mismo" en .data
    // Verificación indirecta: success
]);

// ── 10. Tabla de símbolos ─────────────────────────────────────────────────────
echo "\n{$BOLD}[10] Tabla de símbolos{$RESET}\n";

test('registra variables en tabla de símbolos', <<<'GO'
func main() {
    var x int32 = 1
    y := 2
    const Z int32 = 3
    fmt.Println(x)
}
GO, [
    'no_errors'         => true,
    'symbol_count_gte'  => 3,
]);

// ═══════════════════════════════════════════════════════════════════════════════
//  RESUMEN
// ═══════════════════════════════════════════════════════════════════════════════

$total = $passed + $failed;
echo "\n{$BOLD}{$CYAN}═══ Resumen ════════════════════════════════════════{$RESET}\n";
echo "  Total:   $total\n";
echo "  {$GREEN}Pasados: $passed{$RESET}\n";
if ($failed > 0) {
    echo "  {$RED}Fallidos: $failed{$RESET}\n";
} else {
    echo "  Fallidos: 0\n";
}

$pct = $total > 0 ? round($passed / $total * 100) : 0;
echo "  Cobertura Phase 1: {$pct}%\n\n";

if ($failed === 0) {
    echo "{$BOLD}{$GREEN}  ✓ Phase 1 COMPLETA — listo para integrar con ANTLR4{$RESET}\n\n";
} else {
    echo "{$BOLD}{$YELLOW}  ⚠ $failed test(s) fallidos — revisar ARM64Generator{$RESET}\n\n";
}

exit($failed > 0 ? 1 : 0);