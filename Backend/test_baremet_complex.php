<?php
require_once __DIR__ . '/vendor/autoload.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GOLAMPI'
func main() {
    x := 10
    y := 20
    z := x + y
    fmt.Println(z)
}
GOLAMPI;

$handler = new CompilationHandler();
$result = $handler->compile($code, null);

if (!isset($result['success']) || !$result['success']) {
    echo "✗ Compilation errors:\n";
    if (isset($result['errors'])) {
        foreach ($result['errors'] as $err) {
            echo "  [{$err['type']}] {$err['description']}\n";
        }
    }
} else {
    echo "✓ Compilation successful\n\n";
    $lines = explode("\n", $result['assembly']);
    $in_start = false;
    foreach ($lines as $line) {
        if (strpos($line, '_start:') !== false) {
            $in_start = true;
        }
        if ($in_start) {
            echo $line . "\n";
        }
    }
}
