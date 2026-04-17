<?php
require_once __DIR__ . '/vendor/autoload.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GOLAMPI'
func main() {
    a := 8
    b := 7
    if a > b {
        fmt.Println(a)
    } else {
        fmt.Println(b)
    }
}
GOLAMPI;

$handler = new CompilationHandler();
$result = $handler->compile($code, null);

if (!isset($result['success']) || !$result['success']) {
    echo "✗ Errores de compilación:\n";
    if (isset($result['errors'])) {
        foreach ($result['errors'] as $err) {
            echo "  [{$err['type']}] {$err['description']} (L{$err['line']}, C{$err['column']})\n";
        }
    } else {
        print_r($result);
    }
} else {
    echo "✓ Compilación exitosa\n\n";
    echo "=== ARM64 Assembly ===\n";
    echo $result['assembly'] . "\n";
}
