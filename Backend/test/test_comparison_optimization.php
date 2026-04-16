<?php
/**
 * Prueba de optimización: comparaciones directas con saltos en if
 * 
 * Verifica que las comparaciones generen saltos directos (cmp + b.cond)
 * en lugar de bool intermediarios (cset + cbz).
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Golampi\Compiler\CompilationHandler;

// Código Golampi con comparación en if
$code = <<<'GOLAMPI'
package main

func main() {
    x := 10
    y := 5
    
    if x > y {
        x = x + 1
    } else {
        x = x - 1
    }
}
GOLAMPI;

echo "═══════════════════════════════════════════\n";
echo " Comparación Optimizada: if x > y \n";
echo "═══════════════════════════════════════════\n\n";

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

// Contar líneas totales
$lines = explode("\n", trim($asm));
$totalLines = count($lines);

echo "📊 Estadísticas Assembly:\n";
echo "  Total líneas: $totalLines\n";

// Buscar comparación directa
$hasDirectJump = false;
$compareLine = -1;
$jumpLine = -1;

foreach ($lines as $idx => $line) {
    if (str_contains($line, 'cmp')) {
        $compareLine = $idx;
        echo "\n  ✓ Comparación encontrada (línea " . ($idx + 1) . "): $line\n";
    }
    if (preg_match('/^\s*b\.(gt|le|lt|ge|eq|ne)/', $line)) {
        $jumpLine = $idx;
        $hasDirectJump = true;
        echo "  ✓ Salto directo encontrado (línea " . ($idx + 1) . "): $line\n";
    }
}

// Verificar que NO hay cset innecesario después de la comparación
$hasUnnecessaryCset = false;
if ($compareLine >= 0) {
    for ($i = $compareLine; $i < min($compareLine + 5, count($lines)); $i++) {
        if (str_contains($lines[$i], 'cset x0')) {
            $hasUnnecessaryCset = true;
            echo "  ⚠️  cset encontrado después de cmp (línea " . ($i + 1) . "): {$lines[$i]}\n";
        }
    }
}

echo "\n";
if ($hasDirectJump && !$hasUnnecessaryCset) {
    echo "✅ OPTIMIZACIÓN EXITOSA:\n";
    echo "   - Comparación directa con salto\n";
    echo "   - Sin bool intermediario\n";
} elseif (!$hasDirectJump) {
    echo "❌ OPTIMIZACIÓN NO APLICADA:\n";
    echo "   - No se encontró salto directo\n";
    echo "   - Posiblemente usando cset + cbz (antigua implementación)\n";
} else {
    echo "⚠️  COMPILACIÓN PARCIAL:\n";
    echo "   - Salto directo presente pero también hay cset innecesario\n";
}

echo "\n══════════════════════════════════════════\n";
echo "Assembly generado:\n";
echo "══════════════════════════════════════════\n";
echo $asm;
?>
