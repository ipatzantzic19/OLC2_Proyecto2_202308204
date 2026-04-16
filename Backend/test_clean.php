<?php
require_once 'vendor/autoload.php';

$code = <<<'GOLAMPI'
func main() {
    var a [5]int32;
    a[0] = 42;
    a[1] = 100;
}
GOLAMPI;

$compiler = new \Compiler\ARM64\GolampiCompiler();
$result = $compiler->compile($code);

if ($result->isSuccess()) {
    echo "✓ Code compiled successfully\n";
    echo "\nGenerated Assembly:\n";
    echo $result->getAssembly();
} else {
    echo "✗ Compilation failed\n";
    foreach ($result->getErrors() as $error) {
        echo "  " . $error['type'] . " at line " . $error['line'] . ": " . $error['message'] . "\n";
    }
}
