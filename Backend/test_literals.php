<?php
require_once __DIR__ . '/vendor/autoload.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GOLAMPI'
func main() {
    fmt.Println(42)
    fmt.Println("hello")
    fmt.Println(true)
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
    echo $result['assembly'] . "\n";
}
