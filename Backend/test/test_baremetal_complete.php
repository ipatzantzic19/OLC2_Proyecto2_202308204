<?php
/**
 * Test completo: Bare-metal _start com syscalls para todos los tipos
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
    a := 42
    b := 3.14
    c := 65
    d := true
    
    fmt.Println(a)
    fmt.Println(b)
    fmt.Println(c)
    fmt.Println(d)
}
GOLAMPI;

echo "═════════════════════════════════════════════════════════════════\n";
echo "  TEST: Bare-metal _start - Todos los tipos de datos\n";
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

// Análisis
$lines = explode("\n", trim($asm));
$totalLines = count($lines);

echo "📊 ANÁLISIS:\n";
echo "  Total líneas: $totalLines\n";

$stats = [
    'svc #0' => 0,
    '.itoa_internal' => 0,
    '.ftoa_internal' => 0,
    '_start:' => 0,
    '.global _start' => 0,
];

foreach ($lines as $line) {
    foreach ($stats as $key => &$count) {
        if (str_contains($line, $key)) {
            $count++;
        }
    }
}

echo "\n✅ CARACTERÍSTICAS BAR-METAL:\n";
foreach ($stats as $feature => $count) {
    $status = $count > 0 ? '✓' : '✗';
    echo "  $status $feature: $count ocurrencias\n";
}

echo "\n═════════════════════════════════════════════════════════════════\n";
echo "ASSEMBLY COMPLETO:\n";
echo "═════════════════════════════════════════════════════════════════\n";
echo $asm;
?>
