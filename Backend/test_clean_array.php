<?php
$root = dirname(__DIR__);
require_once 'vendor/autoload.php';
require_once 'generated/GolampiLexer.php';
require_once 'generated/GolampiParser.php';
require_once 'generated/GolampiVisitor.php';
require_once 'generated/GolampiBaseVisitor.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GOLAMPI'
func main() {
    var a [5]int32;
    a[0] = 42;
}
GOLAMPI;

$handler = new CompilationHandler();
$result = $handler->compile($code);

$asm = $result['assembly'] ?? '';
$errors = $result['errors'] ?? [];

if (empty($errors)) {
    echo "✓ Compilation succeeded\n";
    echo "\nAssembly output:\n";
    echo $asm;
} else {
    echo "✗ Compilation failed\n";
    foreach ($errors as $err) {
        echo "  " . $err['type'] . " at line " . $err['line'] . ": " . $err['message'] . "\n";
    }
}
