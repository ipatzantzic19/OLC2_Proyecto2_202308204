#!/usr/bin/env php
<?php

/**
 * Script de prueba del compilador de Golampi → ARM64
 * Analiza archivos .golampi, compila a ARM64 y mantiene un reporte detallado
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar clases generadas por ANTLR
require_once __DIR__ . '/../generated/GolampiLexer.php';
require_once __DIR__ . '/../generated/GolampiParser.php';
require_once __DIR__ . '/../generated/GolampiVisitor.php';
require_once __DIR__ . '/../generated/GolampiBaseVisitor.php';

use Golampi\Compiler\CompilationHandler;
use Golampi\Traits\ErrorHandler;

// Colores ANSI globales
$GREEN  = "\033[0;32m";
$RED    = "\033[0;31m";
$YELLOW = "\033[0;33m";
$CYAN   = "\033[0;36m";
$BLUE   = "\033[0;34m";
$RESET  = "\033[0m";
$BOLD   = "\033[1m";

/**
 * Formato para tabla de errores
 */
function formatErrorTable(array $errors): string
{
    if (empty($errors)) {
        return "✅ Sin errores\n";
    }

    $output = "\n" . str_repeat("=", 120) . "\n";
    $output .= "REPORTE DE ERRORES DE COMPILACIÓN\n";
    $output .= str_repeat("=", 120) . "\n";
    $output .= sprintf("%-5s %-20s %-60s %-10s\n", "#", "Tipo", "Descripción", "Línea");
    $output .= str_repeat("-", 120) . "\n";

    foreach ($errors as $idx => $error) {
        $type = $error['type'] ?? 'Unknown';
        $desc = substr($error['description'] ?? '', 0, 60);
        $line = $error['line'] ?? 0;
        
        $output .= sprintf(
            "%-5s %-20s %-60s %-10s\n",
            $idx + 1,
            $type,
            $desc,
            $line
        );
    }

    $output .= str_repeat("=", 120) . "\n";
    return $output;
}

/**
 * Formato para tabla de símbolos
 */
function formatSymbolTable(array $symbols): string
{
    if (empty($symbols)) {
        return "⚠️  Tabla de símbolos vacía\n";
    }

    $output = "\n" . str_repeat("=", 140) . "\n";
    $output .= "TABLA DE SÍMBOLOS (COMPILACIÓN)\n";
    $output .= str_repeat("=", 140) . "\n";
    $output .= sprintf(
        "%-20s %-15s %-15s %-30s %-8s %-15s %-8s\n",
        "Identificador",
        "Tipo",
        "Ámbito",
        "Valor",
        "Línea",
        "Scope Depth",
        "Columna"
    );
    $output .= str_repeat("-", 140) . "\n";

    foreach ($symbols as $symbol) {
        $value = $symbol['value'] ?? '';
        if (is_array($value)) {
            $value = json_encode($value);
        }
        
        $output .= sprintf(
            "%-20s %-15s %-15s %-30s %-8s %-15s %-8s\n",
            substr($symbol['identifier'] ?? 'unknown', 0, 20),
            substr($symbol['type'] ?? 'any', 0, 15),
            substr($symbol['scope'] ?? 'global', 0, 15),
            substr((string)$value, 0, 30),
            $symbol['line'] ?? 0,
            $symbol['scopeDepth'] ?? 0,
            $symbol['column'] ?? 0
        );
    }

    $output .= str_repeat("=", 140) . "\n";
    return $output;
}

/**
 * Función para compilar un archivo Golampi
 */
function compileGolampiFile(string $filePath): array
{
    global $GREEN, $RED, $YELLOW, $CYAN, $BLUE, $RESET, $BOLD;
    
    $result = [
        'success' => false,
        'assembly' => '',
        'errors' => [],
        'symbolTable' => [],
        'compilationTime' => 0,
        'assemblyLineCounts' => [],
        'message' => ''
    ];

    $startTime = microtime(true);

    try {
        // Verificar que el archivo existe
        if (!file_exists($filePath)) {
            throw new Exception("Archivo no encontrado: $filePath");
        }

        // Verificar que es un archivo válido
        if (!is_file($filePath)) {
            throw new Exception("La ruta especificada no es un archivo: $filePath");
        }

        // Leer el código fuente
        $sourceCode = file_get_contents($filePath);
        
        if ($sourceCode === false) {
            throw new Exception("Error al leer el archivo: $filePath");
        }

        echo "\n{$BLUE}📄 Archivo:{$RESET} " . basename($filePath) . "\n";
        echo "{$BLUE}📊 Tamaño:{$RESET} " . strlen($sourceCode) . " caracteres\n";
        echo "{$BLUE}📝 Líneas:{$RESET} " . (substr_count($sourceCode, "\n") + 1) . "\n";

        // Compilar usando CompilationHandler
        $handler = new CompilationHandler();
        $compilationResult = $handler->compile($sourceCode);

        // Extraer resultados de la compilación
        $result['assembly'] = $compilationResult['assembly'] ?? '';
        $result['errors'] = $compilationResult['errors'] ?? [];
        $result['symbolTable'] = $compilationResult['symbolTable'] ?? [];
        
        // Contar líneas en el assembly
        $result['assemblyLineCounts'] = [
            'total' => substr_count($result['assembly'], "\n"),
            'hasData' => substr_count($result['assembly'], ".data"),
            'hasText' => substr_count($result['assembly'], ".text"),
        ];

        $result['success'] = empty($result['errors']);
        $result['compilationTime'] = microtime(true) - $startTime;

        if ($result['success']) {
            $result['message'] = "✅ Compilación completada exitosamente";
        } else {
            $result['message'] = "⚠️  Compilación completada con errores";
        }

    } catch (\Throwable $e) {
        $result['message'] = "❌ Error fatal: " . $e->getMessage();
        $result['errors'][] = [
            'type' => 'Fatal',
            'description' => $e->getMessage(),
            'line' => 0,
            'column' => 0
        ];
        $result['compilationTime'] = microtime(true) - $startTime;
    }

    return $result;
}

/**
 * Función para mostrar el assembly generado (primeras líneas o completo)
 */
function showAssemblyPreview(string $assembly, int $maxLines = 30): string
{
    $lines = explode("\n", $assembly);
    $preview = array_slice($lines, 0, $maxLines);
    
    $output = "\n" . str_repeat("-", 120) . "\n";
    if ($maxLines >= count($lines)) {
        $output .= "📦 CÓDIGO ENSAMBLADOR ARM64 COMPLETO (" . count($lines) . " líneas):\n";
    } else {
        $output .= "📦 PREVIEW DEL ASSEMBLY (máximo $maxLines de " . count($lines) . " líneas):\n";
    }
    $output .= str_repeat("-", 120) . "\n";
    
    foreach ($preview as $lineNum => $line) {
        $output .= sprintf("%5d | %s\n", $lineNum + 1, $line);
    }
    
    $remainingLines = count($lines) - $maxLines;
    if ($remainingLines > 0) {
        $output .= sprintf("\n... (%d líneas más - ver archivo .s guardado) ...\n", $remainingLines);
    }
    
    $output .= str_repeat("-", 120) . "\n";
    return $output;
}

/**
 * Función para mostrar TODAS las líneas del assembly generado
 */
function showCompleteAssembly(string $assembly): string
{
    $lines = explode("\n", $assembly);
    
    $output = "\n" . str_repeat("=", 140) . "\n";
    $output .= "📦 CÓDIGO ENSAMBLADOR ARM64 COMPLETO (" . count($lines) . " líneas totales):\n";
    $output .= str_repeat("=", 140) . "\n";
    
    foreach ($lines as $lineNum => $line) {
        $output .= sprintf("%5d | %s\n", $lineNum + 1, $line);
    }
    
    $output .= str_repeat("=", 140) . "\n";
    return $output;
}

// ============================================================================
// MAIN EXECUTION
// ============================================================================

global $GREEN, $RED, $YELLOW, $CYAN, $BLUE, $RESET, $BOLD;

echo str_repeat("=", 120) . "\n";
echo "                    COMPILADOR GOLAMPI → ARM64 - SISTEMA DE PRUEBAS\n";
echo str_repeat("=", 120) . "\n";

// Determinar el archivo a ejecutar
$testFile = $argv[1] ?? null;

if ($testFile === null) {
    echo "{$YELLOW}⚠️  No se especificó un archivo{$RESET}\n";
    echo "\n{$BOLD}Uso:{$RESET}\n";
    echo "  php test_compile.php [ruta/archivo.go]\n\n";
    echo "{$BOLD}Ejemplos:{$RESET}\n";
    echo "  php test_compile.php ./calificacionPrueba/embebidas.go\n";
    echo "  php test_compile.php ../samples/hello.go\n";
    exit(1);
}

// Soportar directorios - buscar archivos .go
if (is_dir($testFile)) {
    echo "{$BLUE}📁 Directorio especificado:{$RESET} $testFile\n";
    $files = glob("$testFile/*.go");
    
    if (empty($files)) {
        echo "{$RED}❌ No se encontraron archivos .go en: $testFile{$RESET}\n";
        exit(1);
    }
    
    echo "{$GREEN}✓ Se encontraron " . count($files) . " archivo(s){$RESET}\n";
    $testFile = $files[0];
    echo "{$BLUE}📄 Compilando:{$RESET} " . basename($testFile) . "\n";
}

if (!file_exists($testFile)) {
    echo "{$RED}❌ Error: El archivo '$testFile' no existe{$RESET}\n";
    exit(1);
}

// Compilar el archivo
$result = compileGolampiFile($testFile);

// Mostrar resultados
echo "\n" . str_repeat("=", 120) . "\n";
echo "RESULTADOS DE LA COMPILACIÓN\n";
echo str_repeat("=", 120) . "\n";
echo "Estado: " . $result['message'] . "\n";
echo "Tiempo de compilación: " . number_format($result['compilationTime'] * 1000, 2) . " ms\n";
echo "Líneas de assembly: " . $result['assemblyLineCounts']['total'] . "\n";

// Mostrar errores
if (!empty($result['errors'])) {
    echo formatErrorTable($result['errors']);
}

// Mostrar tabla de símbolos
if (!empty($result['symbolTable'])) {
    echo formatSymbolTable($result['symbolTable']);
}

// Mostrar COMPLETO assembly
if (!empty($result['assembly'])) {
    echo showCompleteAssembly($result['assembly']);
}

// Resumen final
echo "\n" . str_repeat("=", 120) . "\n";
echo "RESUMEN DE COMPILACIÓN\n";
echo str_repeat("=", 120) . "\n";
echo "Total de errores: " . count($result['errors']) . "\n";
echo "Total de símbolos: " . count($result['symbolTable']) . "\n";
echo "Estado final: " . ($result['success'] ? "{$GREEN}✅ COMPILACIÓN EXITOSA{$RESET}" : "{$RED}❌ CON ERRORES{$RESET}") . "\n";
echo str_repeat("=", 120) . "\n\n";

// Opción para guardar assembly completo
if (!empty($result['assembly']) && $result['success']) {
    $outputFile = pathinfo($testFile, PATHINFO_DIRNAME) . '/' . pathinfo($testFile, PATHINFO_FILENAME) . '.s';
    file_put_contents($outputFile, $result['assembly']);
    echo "{$GREEN}✓ Assembly guardado en:{$RESET} $outputFile\n";
}

exit($result['success'] ? 0 : 1);
