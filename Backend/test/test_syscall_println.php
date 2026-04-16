<?php
/**
 * Test: Verificar que fmt.Println ahora usa syscalls en lugar de printf
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../generated/GolampiLexer.php';
require_once __DIR__ . '/../generated/GolampiParser.php';
require_once __DIR__ . '/../generated/GolampiVisitor.php';
require_once __DIR__ . '/../generated/GolampiBaseVisitor.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GOLAMPI'
package main

func main() {
    x := 42
    fmt.Println(x)
}
GOLAMPI;

echo "═════════════════════════════════════════════════════════════════\n";
echo "  TEST: fmt.Println → Syscall (bare-metal _start)\n";
echo "═════════════════════════════════════════════════════════════════\n\n";

$handler = new CompilationHandler();
$result  = $handler->compile($code);

if (!empty($result['errors'])) {
    echo "❌ COMPILACIÓN FALLIDA:\n";
    foreach ($result['errors'] as $err) {
        $msg = is_array($err) ? $err['description'] ?? $err : $err;
        echo "  - $msg\n";
    }
    exit(1);
}

$asm = $result['assembly'] ?? '';

echo "📊 ANÁLISIS DEL ASSEMBLY:\n\n";

// Verificar presencia de características bare-metal
$checks = [
    '.global _start' => 'Punto de entrada bare-metal: .global _start',
    'svc #0' => 'Llamadas syscall: svc #0',
    'x8, #64' => 'Syscall write (#64): mov x8, #64',
    'bl printf' => 'Llamadas printf (antiguo)',
];

$hasChecks = [];
foreach ($checks as $pattern => $description) {
    $present = str_contains($asm, $pattern);
    $hasChecks[$pattern] = $present;
    $status = $present ? '✓' : '✗';
    echo "  $status $description\n";
}

echo "\n✅ VERIFICACIÓN:\n";

if ($hasChecks['.global _start'] && $hasChecks['svc #0'] && !$hasChecks['bl printf']) {
    echo "   ✓ Assembly genera bare-metal syscalls (SIN printf).\n";
    echo "   ✓ Punto de entrada cambiado a _start.\n";
    echo "   ✓ Disposición lista para ejecución bare-metal.\n";
} elseif ($hasChecks['bl printf']) {
    echo "   ✗ Sigue usando printf (no es bare-metal).\n";
} else {
    echo "   ? Estructura incompletamente generada.\n";
}

echo "\n══════════════════════════════════════════════════════════════\n";
echo "PRIMERAS 80 LÍNEAS DEL ASSEMBLY:\n";
echo "══════════════════════════════════════════════════════════════\n";

$lines = explode("\n", $asm);
$printLines = array_slice($lines, 0, 80);
echo implode("\n", $printLines);

echo "\n\n(... más lineas ...)\n";
?>
