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

$handler = new \Golampi\Compiler\CompilationHandler();
$result = $handler->compile($code);

echo "═══════════════════════════════════════════════════════════\n";
echo "Compilación de función multi-parámetro\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "Errores encontrados: " . count($result['errors']) . "\n";
foreach ($result['errors'] as $err) {
    echo "  [Línea " . $err['line'] . "] [" . $err['type'] . "] " . $err['description'] . "\n";
}

echo "\nAssembly generado: " . strlen($result['assembly']) . " bytes\n";
echo "Éxito: " . ($result['success'] ? 'SÍ' : 'NO') . "\n";
