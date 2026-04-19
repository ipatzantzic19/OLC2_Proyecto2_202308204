#!/usr/bin/env php
<?php

/**
 * Test de Tabla de Símbolos
 * 
 * Muestra la tabla de símbolos generada durante la compilación
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar clases generadas por ANTLR
require_once __DIR__ . '/../generated/GolampiLexer.php';
require_once __DIR__ . '/../generated/GolampiParser.php';
require_once __DIR__ . '/../generated/GolampiVisitor.php';
require_once __DIR__ . '/../generated/GolampiBaseVisitor.php';

use Golampi\Compiler\CompilationHandler;

// Colores ANSI
const GREEN  = "\033[0;32m";
const RED    = "\033[0;31m";
const YELLOW = "\033[0;33m";
const CYAN   = "\033[0;36m";
const BLUE   = "\033[0;34m";
const RESET  = "\033[0m";
const BOLD   = "\033[1m";

if ($argc < 2) {
    echo "Uso: php test_symbol_table.php <archivo.go>\n";
    exit(1);
}

$sourceFile = $argv[1];

if (!file_exists($sourceFile)) {
    echo RED . "❌ Archivo no encontrado: $sourceFile" . RESET . "\n";
    exit(1);
}

$sourceCode = file_get_contents($sourceFile);
if ($sourceCode === false) {
    echo RED . "❌ Error al leer: $sourceFile" . RESET . "\n";
    exit(1);
}

echo CYAN . "🔍 Analizando tabla de símbolos para: " . basename($sourceFile) . RESET . "\n";
echo str_repeat("=", 100) . "\n\n";

// Compilar
$handler = new CompilationHandler();
$result = $handler->compile($sourceCode);

$symbolTable = $result['symbolTable'] ?? [];
$errors = $result['errors'] ?? [];

// Mostrar errores
if (!empty($errors)) {
    echo RED . "❌ ERRORES DE COMPILACIÓN:" . RESET . "\n";
    foreach ($errors as $error) {
        echo "  [{$error['id']}] {$error['type']} (Línea {$error['line']}, Col {$error['column']}): {$error['description']}\n";
    }
    echo "\n";
}

// Mostrar tabla de símbolos
echo CYAN . BOLD . "📊 TABLA DE SÍMBOLOS:" . RESET . "\n";
echo str_repeat("=", 100) . "\n";

if (empty($symbolTable)) {
    echo YELLOW . "⚠️  La tabla de símbolos está vacía" . RESET . "\n";
} else {
    // Filtrar solo símbolos válidos (con identificador no vacío)
    $validSymbols = array_filter($symbolTable, function($sym) {
        return is_array($sym) && !empty($sym['identifier'] ?? '') 
            && $sym['identifier'] !== 'param' && $sym['identifier'] !== '_start';
    });

    if (empty($validSymbols)) {
        echo YELLOW . "⚠️  No hay símbolos válidos en la tabla" . RESET . "\n";
    } else {
        // Encabezados
        echo sprintf(
            "%-4s | %-20s | %-15s | %-20s | %-15s | %-6s | %-6s\n",
            "#",
            "Identificador",
            "Tipo",
            "Ámbito",
            "Valor",
            "Línea",
            "Columna"
        );
        echo str_repeat("-", 100) . "\n";

        // Filas
        $counter = 1;
        foreach ($validSymbols as $sym) {
            $id = $sym['identifier'] ?? '';
            $type = $sym['type'] ?? '';
            $scope = $sym['scope'] ?? '';
            $value = isset($sym['value']) ? (is_string($sym['value']) ? $sym['value'] : (string)$sym['value']) : 'nil';
            $line = $sym['line'] ?? 0;
            $col = $sym['column'] ?? 0;

            echo sprintf(
                "%-4d | %-20s | %-15s | %-20s | %-15s | %-6d | %-6d\n",
                $counter++,
                substr($id, 0, 19),
                substr($type, 0, 14),
                substr($scope, 0, 19),
                substr($value, 0, 14),
                $line,
                $col
            );
        }
    }
}

echo "\n";
echo str_repeat("=", 100) . "\n";
$validSymbols = array_filter($symbolTable, function($sym) {
    return is_array($sym) && !empty($sym['identifier'] ?? '') 
        && $sym['identifier'] !== 'param' && $sym['identifier'] !== '_start';
});
echo "Total de símbolos válidos: " . GREEN . count($validSymbols) . RESET . "\n";

// Mostrar en formato JSON para análisis
echo "\n" . CYAN . BOLD . "📋 FORMATO JSON:" . RESET . "\n";
echo json_encode(array_values($validSymbols), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
