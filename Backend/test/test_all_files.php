#!/usr/bin/env php
<?php

/**
 * Script de prueba comprehensiva para compilador Golampi
 * Prueba todos los archivos .go con tabla de símbolos y código ARM64
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

// Colores ANSI
$GREEN  = "\033[0;32m";
$RED    = "\033[0;31m";
$YELLOW = "\033[0;33m";
$CYAN   = "\033[0;36m";
$BLUE   = "\033[0;34m";
$RESET  = "\033[0m";
$BOLD   = "\033[1m";

// Estadísticas globales
$stats = [
    'total' => 0,
    'successful' => 0,
    'failed' => 0,
    'times' => [],
    'results' => []
];

/**
 * Formatea la tabla de símbolos
 */
function formatSymbolTable(array $symbols): string
{
    global $BOLD, $RESET;
    
    if (empty($symbols)) {
        return "⚠️  Tabla de símbolos vacía\n";
    }

    $output = "\n" . str_repeat("=", 180) . "\n";
    $output .= "{$BOLD}TABLA DE SÍMBOLOS{$RESET}\n";
    $output .= str_repeat("=", 180) . "\n";
    $output .= sprintf(
        "%-25s %-12s %-12s %-35s %-8s %-12s %-10s %-15s\n",
        "Identificador",
        "Tipo",
        "Ámbito",
        "Valor",
        "Línea",
        "Scope",
        "Columna",
        "Inicializado"
    );
    $output .= str_repeat("-", 180) . "\n";

    $count = 0;
    foreach ($symbols as $symbol) {
        $value = $symbol['value'] ?? '';
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        
        $initialized = isset($symbol['initialized']) ? ($symbol['initialized'] ? 'Sí' : 'No') : '?';
        
        $output .= sprintf(
            "%-25s %-12s %-12s %-35s %-8s %-12s %-10s %-15s\n",
            substr((string)($symbol['identifier'] ?? 'unknown'), 0, 25),
            substr((string)($symbol['type'] ?? 'any'), 0, 12),
            substr((string)($symbol['scope'] ?? 'global'), 0, 12),
            substr((string)$value, 0, 35),
            $symbol['line'] ?? 0,
            $symbol['scopeDepth'] ?? 0,
            $symbol['column'] ?? 0,
            $initialized
        );
        $count++;
    }

    $output .= str_repeat("=", 180) . "\n";
    $output .= "{$BOLD}Total de símbolos: {$count}{$RESET}\n";
    return $output;
}

/**
 * Formatea la tabla de errores
 */
function formatErrorTable(array $errors): string
{
    global $RED, $RESET, $BOLD;
    
    if (empty($errors)) {
        return "✅ Sin errores de compilación\n";
    }

    $output = "\n" . str_repeat("=", 140) . "\n";
    $output .= "{$BOLD}{$RED}REPORTE DE ERRORES DE COMPILACIÓN{$RESET}\n";
    $output .= str_repeat("=", 140) . "\n";
    $output .= sprintf("%-5s %-20s %-70s %-15s\n", "#", "Tipo", "Descripción", "Ubicación");
    $output .= str_repeat("-", 140) . "\n";

    foreach ($errors as $idx => $error) {
        $type = $error['type'] ?? 'Unknown';
        $desc = substr($error['description'] ?? '', 0, 70);
        $loc = isset($error['line']) ? "Línea {$error['line']}" : (isset($error['column']) ? "Col {$error['column']}" : "?");
        
        $output .= sprintf(
            "%-5s %-20s %-70s %-15s\n",
            $idx + 1,
            $type,
            $desc,
            $loc
        );
    }

    $output .= str_repeat("=", 140) . "\n";
    return $output;
}

/**
 * Muestra el código ARM64 con numeración
 */
function formatARM64Code(string $assembly): string
{
    global $CYAN, $RESET, $BOLD;
    
    if (empty($assembly)) {
        return "⚠️  Código ARM64 vacío\n";
    }

    $lines = explode("\n", $assembly);
    $output = "\n" . str_repeat("=", 100) . "\n";
    $output .= "{$BOLD}{$CYAN}CÓDIGO ARM64 GENERADO{$RESET}\n";
    $output .= str_repeat("=", 100) . "\n";

    foreach ($lines as $idx => $line) {
        // Colorear directivas y etiquetas
        if (strpos($line, '.') === 0 || strpos($line, ':') !== false) {
            $line = "{$CYAN}{$line}{$RESET}";
        }
        // Colorear comentarios
        if (strpos(trim($line), ';') === 0 || strpos(trim($line), '#') === 0) {
            $line = "\033[0;90m{$line}{$RESET}";
        }
        
        $output .= sprintf("%5d │ %s\n", $idx + 1, $line);
    }

    $lineCount = count($lines);
    $output .= str_repeat("=", 100) . "\n";
    $output .= "{$BOLD}Total de líneas: {$lineCount}{$RESET}\n";
    return $output;
}

/**
 * Compila un archivo Golampi
 */
function compileFile(string $filePath): array
{
    global $GREEN, $RED, $YELLOW, $CYAN, $BLUE, $RESET, $BOLD;
    
    $result = [
        'success' => false,
        'assembly' => '',
        'errors' => [],
        'symbolTable' => [],
        'compilationTime' => 0,
        'message' => '',
        'lineCount' => 0,
        'charCount' => 0
    ];

    $startTime = microtime(true);

    try {
        if (!file_exists($filePath)) {
            throw new Exception("Archivo no encontrado: $filePath");
        }

        $sourceCode = file_get_contents($filePath);
        if ($sourceCode === false) {
            throw new Exception("Error al leer el archivo");
        }

        // Información del archivo
        $result['charCount'] = strlen($sourceCode);
        $result['lineCount'] = substr_count($sourceCode, "\n") + 1;

        // Compilar
        $handler = new CompilationHandler();
        $compilationResult = $handler->compile($sourceCode);

        $result['compilationTime'] = microtime(true) - $startTime;
        $result['success'] = $compilationResult['success'] ?? false;
        $result['assembly'] = $compilationResult['assembly'] ?? '';
        $result['errors'] = $compilationResult['errors'] ?? [];
        $result['symbolTable'] = $compilationResult['symbolTable'] ?? [];

    } catch (Exception $e) {
        $result['compilationTime'] = microtime(true) - $startTime;
        $result['message'] = "Excepción: " . $e->getMessage();
        $result['errors'][] = [
            'type' => 'Exception',
            'description' => $e->getMessage(),
            'line' => 0
        ];
    }

    return $result;
}

/**
 * Función principal
 */
function main()
{
    global $GREEN, $RED, $YELLOW, $CYAN, $BLUE, $RESET, $BOLD, $stats;

    echo "\n";
    echo str_repeat("=", 100) . "\n";
    echo "{$BOLD}{$BLUE}🔧 PRUEBA COMPREHENSIVA DEL COMPILADOR GOLAMPI{$RESET}\n";
    echo str_repeat("=", 100) . "\n";
    echo "Directorio: " . __DIR__ . "\n";
    echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
    echo str_repeat("=", 100) . "\n\n";

    // Encontrar todos los archivos .go
    $goFiles = glob(__DIR__ . '/test_*.go');
    if (empty($goFiles)) {
        echo "{$RED}❌ No se encontraron archivos test_*.go{$RESET}\n";
        return;
    }

    // Ordenar archivos
    sort($goFiles);

    $stats['total'] = count($goFiles);

    echo "{$CYAN}📂 Archivos encontrados: {$stats['total']}{$RESET}\n\n";

    foreach ($goFiles as $idx => $filePath) {
        $filename = basename($filePath);
        $num = $idx + 1;

        echo "\n";
        echo str_repeat("-", 100) . "\n";
        echo "{$BOLD}[{$num}/{$stats['total']}] Compilando: {$CYAN}{$filename}{$RESET}\n";
        echo str_repeat("-", 100) . "\n";

        $result = compileFile($filePath);

        // Información básica del archivo
        echo "{$BLUE}Tamaño:{$RESET} {$result['charCount']} caracteres, {$result['lineCount']} líneas\n";
        echo "{$BLUE}Tiempo:{$RESET} " . round($result['compilationTime'] * 1000, 2) . " ms\n";

        // Resultado de compilación
        if ($result['success']) {
            echo "{$GREEN}✅ COMPILACIÓN EXITOSA{$RESET}\n";
            $stats['successful']++;
        } else {
            echo "{$RED}❌ COMPILACIÓN FALLIDA{$RESET}\n";
            $stats['failed']++;
        }

        // Errores
        if (!empty($result['errors'])) {
            echo "\n{$RED}Errores encontrados: " . count($result['errors']) . "{$RESET}";
            echo formatErrorTable($result['errors']);
        }

        // Tabla de símbolos
        if (!empty($result['symbolTable'])) {
            echo formatSymbolTable($result['symbolTable']);
        } else {
            echo "\n⚠️  Tabla de símbolos vacía\n";
        }

        // Código ARM64
        if (!empty($result['assembly'])) {
            echo formatARM64Code($result['assembly']);
        } else {
            echo "\n⚠️  No se generó código ARM64\n";
        }

        $stats['results'][$filename] = $result;
        $stats['times'][$filename] = $result['compilationTime'];
    }

    // Resumen final
    printSummary();
}

/**
 * Imprime el resumen final
 */
function printSummary()
{
    global $GREEN, $RED, $YELLOW, $CYAN, $BLUE, $RESET, $BOLD, $stats;

    echo "\n\n";
    echo str_repeat("=", 100) . "\n";
    echo "{$BOLD}{$BLUE}📊 RESUMEN DE PRUEBAS{$RESET}\n";
    echo str_repeat("=", 100) . "\n";

    echo sprintf(
        "Total de archivos: %d | {$GREEN}Exitosos: %d{$RESET} | {$RED}Fallidos: %d{$RESET}\n",
        $stats['total'],
        $stats['successful'],
        $stats['failed']
    );

    if ($stats['successful'] > 0) {
        $avgTime = (array_sum($stats['times']) / count($stats['times'])) * 1000;
        echo "Tiempo promedio de compilación: " . round($avgTime, 2) . " ms\n";
    }

    // Tabla de tiempos
    if (!empty($stats['times'])) {
        echo "\n" . str_repeat("=", 100) . "\n";
        echo "{$BOLD}TIEMPOS DE COMPILACIÓN (orden descendente){$RESET}\n";
        echo str_repeat("=", 100) . "\n";
        echo sprintf("%-40s %-30s %-20s\n", "Archivo", "Tiempo (ms)", "Símbolos");
        echo str_repeat("-", 100) . "\n";

        arsort($stats['times']);
        foreach ($stats['times'] as $filename => $time) {
            $symbolCount = count($stats['results'][$filename]['symbolTable'] ?? []);
            echo sprintf(
                "%-40s %-30.2f %-20d\n",
                substr($filename, 0, 40),
                $time * 1000,
                $symbolCount
            );
        }
    }

    echo "\n" . str_repeat("=", 100) . "\n";
    echo "{$GREEN}✅ PRUEBAS COMPLETADAS{$RESET}\n";
    echo str_repeat("=", 100) . "\n\n";
}

// Ejecutar
main();
