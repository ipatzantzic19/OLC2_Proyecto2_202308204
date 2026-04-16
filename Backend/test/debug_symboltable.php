<?php
/**
 * Script de depuración para verificar la tabla de símbolos
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Golampi\Compiler\CompilationHandler;

$code = <<<'GO'
func main() {
    var m [2][3]int32
    m[0][0] = 1
}
GO;

echo "═══════════════════════════════════════════════════════════════\n";
echo "Código a compilar:\n";
echo "───────────────────────────────────────────────────────────────\n";
echo $code . "\n";
echo "───────────────────────────────────────────────────────────────\n\n";

$handler = new CompilationHandler();

// Compilar
$result = $handler->compile($code);

echo "Resultado de compilation:\n";
echo "  ✓ Assembly: " . (strlen($result['assembly']) > 0 ? "Generado (" . strlen($result['assembly']) . " bytes)" : "VACÍO") . "\n";
echo "  ✓ Errors: " . count($result['errors']) . "\n";
echo "  ✓ Symbol table entries: " . count($result['symbolTable'] ?? []) . "\n\n";

if (!empty($result['errors'])) {
    echo "Errores encontrados:\n";
    foreach ($result['errors'] as $error) {
        echo "  ✗ " . $error['description'] . "\n";
    }
    echo "\n";
}

echo "Tabla de símbolos:\n";
if (!empty($result['symbolTable'] ?? [])) {
    foreach ($result['symbolTable'] as $name => $info) {
        echo "  ✓ $name:\n";
        if (is_array($info)) {
            foreach ($info as $key => $value) {
                $val_str = is_array($value) ? json_encode($value) : $value;
                echo "      - $key: $val_str\n";
            }
        }
    }
} else {
    echo "  (vacía)\n\n";
    echo "Verificando internamente...\n";
    
    // Verificar qué está en el resultado
    echo "  Keys de resultado: " . implode(", ", array_keys($result)) . "\n\n";
    
    // Simular compilación manual para ver qué sucede
    echo "Intentando compilación manual con depuración...\n";
    
    // Acceso directo a ARM64Generator
    $parser = new \GolampiParser(new \CommonTokenStream(new \GolampiLexer(new \InputStream($code))));
    $ast = $parser->program();
    
    $gen = new \Golampi\Compiler\ARM64\ARM64Generator();
    $manualResult = $gen->compile($ast);
    
    echo "  Symbol table (resultado manual): " . count($manualResult['symbols'] ?? []) . " entries\n";
    
    if (!empty($manualResult['symbols'] ?? [])) {
        foreach ($manualResult['symbols'] as $name => $info) {
            echo "    ✓ $name\n";
        }
    }
}

echo "\nFin de depuración.\n";
