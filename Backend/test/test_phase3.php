#!/usr/bin/env php
<?php
/**
 * Test de Fase 3 — Arrays multidimensionales (Compilador Golampi → ARM64)
 *
 * Valida: arrays 1D/2D/3D, indexación dinámica, lectura/escritura,
 * inicializadores de arrays.
 *
 * Uso: php test_phase3.php
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
    global $passed, $failed, $GREEN, $RED, $YELLOW, $CYAN, $RESET, $BOLD;

    $result = compile($code);
    $asm    = $result['assembly'] ?? '';
    $errors = $result['errors']   ?? [];

    $ok       = true;
    $messages = [];

    foreach ($expectations as $key => $expected) {
        switch ($key) {
            case 'no_errors':
                if (!empty($errors) && $expected) {
                    $ok = false;
                    $errorMsgs = array_map(fn($e) => ($e['type'] ?? '?') . ': ' . ($e['message'] ?? '?'), $errors);
                    $messages[] = "Errores encontrados: " . implode(', ', $errorMsgs);
                }
                break;

            case 'has_errors':
                if (empty($errors) && $expected) {
                    $ok = false;
                    $messages[] = "Se esperaban errores pero la compilación pasó";
                }
                break;

            case 'assembly_contains':
                foreach ((array)$expected as $substring) {
                    if (strpos($asm, $substring) === false) {
                        $ok = false;
                        $messages[] = "Falta en assembly: '$substring'";
                    }
                }
                break;

            case 'assembly_not_contains':
                foreach ((array)$expected as $substring) {
                    if (strpos($asm, $substring) !== false) {
                        $ok = false;
                        $messages[] = "No debería estar en assembly: '$substring'";
                    }
                }
                break;

            case 'symbol_table_has':
                $symbols = $result['symbolTable'] ?? [];
                foreach ((array)$expected as $symName) {
                    if (!isset($symbols[$symName])) {
                        $ok = false;
                        $messages[] = "Símbolo '$symName' no en tabla de símbolos";
                    }
                }
                break;
        }
    }

    $status = $ok ? "$GREEN✓$RESET" : "$RED✗$RESET";
    echo "  $status  $name\n";
    for ($i = 0; $i < count($messages); $i++) {
        echo "       " . ($i + 1) . ". " . $messages[$i] . "\n";
    }

    if ($ok) {
        $passed++;
    } else {
        $failed++;
    }
}

echo "\n$BOLD═══════ Golampi Fase 3 — Arrays $RESET\n\n";

// ════════════════════════════════════════════════════════════════════════════
// [1] ARRAYS 1D — Asignación y lectura
// ════════════════════════════════════════════════════════════════════════════

echo "$CYAN[1] Arrays 1D — Asignación y lectura$RESET\n";

test('Array 1D int32 declaración', <<<'GO'
func main() {
    var a [10]int32
}
GO, ['no_errors' => true]);

test('Array 1D asignación simple', <<<'GO'
func main() {
    var a [10]int32
    a[0] = 42
}
GO, [
    'no_errors'           => true,
    'assembly_contains'   => [
        'str x0, [x3]',    // escritura
    ]
]);

test('Array 1D lectura', <<<'GO'
func main() {
    var a [10]int32
    a[0] = 42
    var x int32 = a[0]
}
GO, [
    'no_errors'         => true,
    'assembly_contains' => [
        'ldr x0, [x3]',  // lectura
    ]
]);

test('Array 1D acceso con índice dinámico', <<<'GO'
func main() {
    var a [5]int32
    var i int32 = 2
    a[i] = 99
}
GO, [
    'no_errors'         => true,
    'assembly_contains' => [
        'lsl x1, x1, #3',  // i * 8
        'add x3, x2, x1',  // addr = base + offset
    ]
]);

// ════════════════════════════════════════════════════════════════════════════
// [2] ARRAYS 2D — Indexación row-major
// ════════════════════════════════════════════════════════════════════════════

echo "\n$CYAN[2] Arrays 2D — Indexación row-major$RESET\n";

test('Array 2D int32 declaración', <<<'GO'
func main() {
    var m [3][4]int32
}
GO, ['no_errors' => true]);

test('Array 2D escritura m[i][j]', <<<'GO'
func main() {
    var m [2][3]int32
    m[0][0] = 1
    m[0][1] = 2
    m[1][2] = 6
}
GO, [
    'no_errors'         => true,
    'assembly_contains' => [
        'str x0, [x3]',  // múltiples escrituras
    ],
    'symbol_table_has'  => ['m'],
]);

test('Array 2D lectura m[i][j]', <<<'GO'
func main() {
    var m [2][3]int32
    m[0][0] = 5
    var x int32 = m[0][0]
}
GO, [
    'no_errors'         => true,
    'assembly_contains' => [
        'ldr x0, [x3]',  // lectura
    ]
]);

test('Array 2D acceso dinámico', <<<'GO'
func main() {
    var m [3][3]int32
    var i, j int32
    m[i][j] = 42
}
GO, [
    'no_errors'         => true,
    'assembly_contains' => [
        'mul x4, x4, x5',   // i * stride (cols)
        'add x1, x1, x4',   // acumular offset
    ]
]);

// ════════════════════════════════════════════════════════════════════════════
// [3] REGRESIÓN — Fase 1 y 2 siguen funcionando
// ════════════════════════════════════════════════════════════════════════════

echo "\n$CYAN[3] Regresión — Fase 1 y 2$RESET\n";

test('Variables simples int32', <<<'GO'
func main() {
    var x int32 = 10
}
GO, ['no_errors' => true]);

test('float32 en Fase 3', <<<'GO'
func main() {
    var f float32 = 3.14
    var g float32 = f + 1.0
}
GO, [
    'no_errors'         => true,
    'assembly_contains' => [
        'fadd s0, s1, s0',  // suma float32 sin cambios
    ]
]);

test('Funciones multi-parámetro', <<<'GO'
func add(a int32, b int32) int32 {
    return a + b
}

func main() {
    var result int32 = add(5, 3)
}
GO, ['no_errors' => true]);

// ════════════════════════════════════════════════════════════════════════════
// RESUMEN
// ════════════════════════════════════════════════════════════════════════════

echo "\n";
echo str_repeat("═", 50) . "\n";
echo "$BOLD═══ Resumen Fase 3 (Arrays) ═══════════════════$RESET\n";
echo str_repeat("═", 50) . "\n";
printf("  Total:    %d\n", $passed + $failed);
printf("  $GREEN✓ Pasados:  %d$RESET\n", $passed);
printf("  $RED✗ Fallidos: %d$RESET\n", $failed);
echo str_repeat("═", 50) . "\n";

if ($failed === 0) {
    echo "\n$BOLD$GREEN✓ Fase 3 Parcial COMPLETA (Arrays 1D/2D)$RESET\n";
} else {
    echo "\n$BOLD$YELLOW⚠ $failed test(s) fallidos — revisar implementación$RESET\n";
}
echo "\n";

exit($failed === 0 ? 0 : 1);
