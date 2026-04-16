<?php
/**
 * Test: Verificar que el punto de entrada es _start pero usa printf normal
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
echo "  Verificación: _start con printf (libc)\n";
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

echo "📊 ANÁLISIS:\n\n";

// Verificaciones
$checks = [
    '.global _start' => 'Punto de entrada es _start',
    'bl printf' => 'Usa printf de libc',
    'svc #0' => 'Usa syscalls directos',
];

$results = [];
foreach ($checks as $pattern => $description) {
    $present = str_contains($asm, $pattern);
    $results[$pattern] = $present;
    $status = $present ? '✓' : '✗';
    echo "  $status $description\n";
}

echo "\n✅ VERIFICACIÓN FINAL:\n";

if ($results['.global _start'] && $results['bl printf'] && !$results['svc #0']) {
    echo "   ✓ Correcto: _start como punto de entrada\n";
    echo "   ✓ Correcto: Usa printf (libc normal)\n";
    echo "   ✓ Correcto: Sin syscalls directos\n";
} else {
    echo "   ✗ Configuración incorrecta\n";
}

echo "\n══════════════════════════════════════════════════════════════\n";
echo "PRIMERAS 60 LÍNEAS DEL ASSEMBLY:\n";
echo "══════════════════════════════════════════════════════════════\n";

$lines = array_slice(explode("\n", $asm), 0, 60);
echo implode("\n", $lines);

echo "\n(...)";
?>
