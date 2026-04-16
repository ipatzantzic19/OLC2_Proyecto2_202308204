<?php
require_once __DIR__ . '/../vendor/autoload.php';

$code = <<<'GO'
func add(a, b int32) int32 {
    return a + b
}

func main() {
    var result int32 = add(5, 3)
}
GO;

echo "═══════════════════════════════════════════════════════════\n";
echo "Análisis del código\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "Código:\n";
echo var_export($code, true);
echo "\n\n";

echo "Líneas del código:\n";
$lines = explode("\n", $code);
foreach ($lines as $idx => $line) {
    echo "  [$idx] " . var_export($line, true) . "\n";
}

echo "\n\nAhora probando con handler...\n";

$handler = new \Golampi\Compiler\CompilationHandler();
$result = $handler->compile($code);

echo "Resultado:\n";
echo "  Errores: " . count($result['errors']) . "\n";

if (count($result['errors']) > 0) {
    echo "\nDetalle de errores:\n";
    foreach ($result['errors'] as $err) {
        echo "    - [Línea " . $err['line'] . "] " . $err['description'] . "\n";
    }
}

echo "\n  Éxito: " . ($result['success'] ? 'SÍ' : 'NO') . "\n";
echo "  Assembly: " . strlen($result['assembly']) . " bytes\n";
