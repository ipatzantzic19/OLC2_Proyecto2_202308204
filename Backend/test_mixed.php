<?php
require_once __DIR__ . '/vendor/autoload.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GOLAMPI'
func main() {
    fmt.Println(8)
    x := 5
    fmt.Println(x)
    fmt.Println(true)
    fmt.Println("test")
}
GOLAMPI;

$handler = new CompilationHandler();
$result = $handler->compile($code, null);

if (!isset($result['success']) || !$result['success']) {
    echo "✗ Compilation errors\n";
} else {
    echo "✓ Success\n\n";
    $lines = explode("\n", $result['assembly']);
    $section = '';
    foreach ($lines as $line) {
        if (strpos($line, '.section') !== false) {
            $section = $line;
            if ($section === '.section .text') break;
        }
        if ($section === '.section .data' || $section === '.section .text') {
            echo $line . "\n";
        }
    }
}
