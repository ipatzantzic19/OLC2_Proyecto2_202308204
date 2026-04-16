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
    var x int32;
    x = 42;
}
GOLAMPI;

$handler = new CompilationHandler();
$result = $handler->compile($code);

$asm = $result['assembly'] ?? '';
$errors = $result['errors'] ?? [];

if (empty($errors)) {
    echo "✓ Scalar compilation succeeded\n";
} else {
    echo "✗ Scalar compilation failed\n";
    foreach ($errors as $err) {
        if (isset($err['message']) && !empty($err['message'])) {
            echo "  " . $err['type'] . " at line " . $err['line'] . ": " . $err['message'] . "\n";
        }
    }
}
