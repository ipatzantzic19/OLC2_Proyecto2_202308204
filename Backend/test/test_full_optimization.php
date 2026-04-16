<?php
/**
 * Prueba el ejemplo del usuario que generaba 63 líneas antes
 * 
 * Verifica que con todas las optimizaciones:
 * 1. Offset=0 para variables simples
 * 2. Saltos directos para comparaciones en if
 * 
 * La salida debería ser significativamente más corta
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar ANTLR
require_once __DIR__ . '/../generated/GolampiLexer.php';
require_once __DIR__ . '/../generated/GolampiParser.php';
require_once __DIR__ . '/../generated/GolampiVisitor.php';
require_once __DIR__ . '/../generated/GolampiBaseVisitor.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GOLAMPI'
package main

func main() {
    a := 10
    b := 20
    if a < b {
        a = a + 5
    } else {
        a = a - 3
    }
}
GOLAMPI;

echo "═══════════════════════════════════════════════════════════════\n";
echo "  PRUEBA: Comparación con Offset=0 + Saltos Directos\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$handler = new CompilationHandler();
$result  = $handler->compile($code);

if (!empty($result['errors'])) {
    echo "❌ COMPILACIÓN FALLIDA:\n";
    foreach ($result['errors'] as $err) {
        echo "  - " . (is_array($err) ? $err['description'] ?? $err : $err) . "\n";
    }
    exit(1);
}

$asm = $result['assembly'] ?? '';
$lines = array_filter(explode("\n", trim($asm)), fn($l) => trim($l) !== '');
$totalLines = count($lines);

echo "📊 RESULT ADOS:\n";
echo "  Total de líneas: $totalLines\n";

// Análisis de la estructura
$statistics = [
    'labels' => 0,
    'cmp' => 0,
    'jumps' => 0,
    'cset' => 0,
    'str' => 0,
    'ldr' => 0,
    'mov' => 0,
    'stp' => 0,
    'ldp' => 0,
];

foreach ($lines as $line) {
    if (str_contains($line, ':')) $statistics['labels']++;
    if (str_contains($line, 'cmp')) $statistics['cmp']++;
    if (preg_match('/^\s*b\./', $line)) $statistics['jumps']++;
    if (str_contains($line, 'cset')) $statistics['cset']++;
    if (str_contains($line, ' str ')) $statistics['str']++;
    if (str_contains($line, ' ldr ')) $statistics['ldr']++;
    if (str_contains($line, ' mov ')) $statistics['mov']++;
    if (str_contains($line, 'stp')) $statistics['stp']++;
    if (str_contains($line, 'ldp')) $statistics['ldp']++;
}

echo "\n📈 INSTRUCCIONES UTILIZADAS:\n";
foreach ($statistics as $instr => $count) {
    if ($count > 0) {
        echo "  - $instr: $count\n";
    }
}

// Análisis de eficiencia
echo "\n✅ VERIFICACIONES:\n";

if ($statistics['cmp'] > 0 && $statistics['jumps'] > 0 && $statistics['cset'] == 0) {
    echo "  ✓ Comparaciones con saltos directos (SIN cset)\n";
} else {
    echo "  ✗ No hay saltos directos o hay cset innecesario\n";
}

if ($statistics['str'] < 10 && $statistics['ldr'] < 10) {
    echo "  ✓ Pocas operaciones de stack (optimización offset=0 activa)\n";
} else {
    echo "  ✗ Demasiadas operaciones stack\n";
}

echo "\n══════════════════════════════════════════════════════════════\n";
echo "ASSEMBLY GENERADO:\n";
echo "══════════════════════════════════════════════════════════════\n";
echo $asm;
?>
