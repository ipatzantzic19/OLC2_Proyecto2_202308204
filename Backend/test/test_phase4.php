#!/usr/bin/env php
<?php

/**
 * Test Phase 4: Compilación completa + Ensamblado + Ejecución
 * 
 * Realiza 4 fases:
 * 1. Compilar archivo .go → generar assembly ARM64 (.s)
 * 2. Guardar assembly a archivo
 * 3. Ensamblar con GNU as (aarch64-linux-gnu-as)
 * 4. Ejecutar el binario generado
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

/**
 * Verifica si un comando existe en PATH.
 */
function command_exists(string $command): bool
{
    $output = [];
    $exitCode = 1;
    exec('command -v ' . escapeshellarg($command) . ' >/dev/null 2>&1', $output, $exitCode);
    return $exitCode === 0;
}

/**
 * Ejecuta un comando de shell y retorna salida + código de salida.
 */
function run_shell_command(string $command): array
{
    $output = [];
    $exitCode = 1;
    exec($command . ' 2>&1', $output, $exitCode);

    return [
        'output' => implode("\n", $output),
        'exitCode' => $exitCode,
    ];
}

/**
 * Crea la estructura de artefactos para un archivo fuente.
 */
function ensure_artifact_structure(string $sourceFile): array
{
    $dir = dirname($sourceFile);
    $base = pathinfo($sourceFile, PATHINFO_FILENAME);

    $artifactRoot = "$dir/generated/$base";
    $srcDir = "$artifactRoot/src";
    $buildDir = "$artifactRoot/build";

    if (!is_dir($srcDir) && !mkdir($srcDir, 0775, true) && !is_dir($srcDir)) {
        throw new Exception("No se pudo crear directorio: $srcDir");
    }

    if (!is_dir($buildDir) && !mkdir($buildDir, 0775, true) && !is_dir($buildDir)) {
        throw new Exception("No se pudo crear directorio: $buildDir");
    }

    return [
        'artifactRoot' => $artifactRoot,
        'srcDir' => $srcDir,
        'buildDir' => $buildDir,
        'base' => $base,
    ];
}

/**
 * FASE 1: Compilar archivo .go a assembly ARM64
 */
function phase1_compile(string $sourceFile): array
{
    echo "\n" . str_repeat("=", 100) . "\n";
    echo CYAN . "FASE 1: COMPILACIÓN (.go → .s)" . RESET . "\n";
    echo str_repeat("=", 100) . "\n";

    $result = [
        'success' => false,
        'assembly' => '',
        'errors' => [],
        'symbolTable' => [],
        'message' => ''
    ];

    try {
        if (!file_exists($sourceFile)) {
            throw new Exception("Archivo no encontrado: $sourceFile");
        }

        $sourceCode = file_get_contents($sourceFile);
        if ($sourceCode === false) {
            throw new Exception("Error al leer: $sourceFile");
        }

        echo BLUE . "📄 Compilando:" . RESET . " " . basename($sourceFile) . "\n";
        echo BLUE . "📊 Tamaño:" . RESET . " " . strlen($sourceCode) . " bytes\n";

        // Compilar
        $handler = new CompilationHandler();
        $compilationResult = $handler->compile($sourceCode);

        $result['assembly'] = $compilationResult['assembly'] ?? '';
        $result['errors'] = $compilationResult['errors'] ?? [];
        $result['symbolTable'] = $compilationResult['symbolTable'] ?? [];
        $result['success'] = empty($result['errors']) && !empty($result['assembly']);

        if ($result['success']) {
            echo GREEN . "✅ Compilación exitosa" . RESET . "\n";
            echo BLUE . "📝 Líneas de assembly:" . RESET . " " . substr_count($result['assembly'], "\n") . "\n";
        } else {
            echo RED . "❌ Errores de compilación:" . RESET . "\n";
            foreach ($result['errors'] as $error) {
                echo "   - Línea " . $error['line'] . ": " . $error['description'] . "\n";
            }
        }

    } catch (Throwable $e) {
        $result['message'] = "Error: " . $e->getMessage();
        $result['success'] = false;
        echo RED . "❌ " . $e->getMessage() . RESET . "\n";
    }

    return $result;
}

/**
 * FASE 2: Guardar assembly a archivo .s
 */
function phase2_save_assembly(string $sourceFile, string $assembly): array
{
    echo "\n" . str_repeat("=", 100) . "\n";
    echo CYAN . "FASE 2: GUARDAR ASSEMBLY (.s)" . RESET . "\n";
    echo str_repeat("=", 100) . "\n";

    $result = [
        'success' => false,
        'assemblyFile' => '',
        'message' => ''
    ];

    try {
        $structure = ensure_artifact_structure($sourceFile);
        $artifactRoot = $structure['artifactRoot'];
        $srcDir = $structure['srcDir'];
        $base = $structure['base'];

        $assemblyFile = "$srcDir/$base.s";

        $bytesWritten = file_put_contents($assemblyFile, $assembly);
        if ($bytesWritten === false) {
            throw new Exception("Error al guardar: $assemblyFile");
        }

        $result['assemblyFile'] = $assemblyFile;
        $result['success'] = true;

        echo BLUE . "💾 Archivo guardado:" . RESET . " " . basename($assemblyFile) . "\n";
        echo BLUE . "📁 Carpeta de artefactos:" . RESET . " $artifactRoot\n";
        echo BLUE . "📊 Tamaño:" . RESET . " $bytesWritten bytes\n";
        echo GREEN . "✅ Assembly guardado correctamente" . RESET . "\n";

    } catch (Throwable $e) {
        $result['message'] = "Error: " . $e->getMessage();
        echo RED . "❌ " . $e->getMessage() . RESET . "\n";
    }

    return $result;
}

/**
 * FASE 3: Ensamblar con GNU as
 */
function phase3_assemble(string $assemblyFile): array
{
    echo "\n" . str_repeat("=", 100) . "\n";
    echo CYAN . "FASE 3: ENSAMBLADO (.s → .o)" . RESET . "\n";
    echo str_repeat("=", 100) . "\n";

    $result = [
        'success' => false,
        'objectFile' => '',
        'message' => '',
        'assemblerOutput' => ''
    ];

    try {
        if (!file_exists($assemblyFile)) {
            throw new Exception("Archivo .s no encontrado: $assemblyFile");
        }

        $srcDir = dirname($assemblyFile);
        $artifactRoot = dirname($srcDir);
        $buildDir = "$artifactRoot/build";

        if (!is_dir($buildDir) && !mkdir($buildDir, 0775, true) && !is_dir($buildDir)) {
            throw new Exception("No se pudo crear directorio build: $buildDir");
        }

        $base = pathinfo($assemblyFile, PATHINFO_FILENAME);
        $objectFile = "$buildDir/$base.o";

        // Intenta diferentes ensambladores
        $assemblers = [
            'aarch64-linux-gnu-as',
            'as'
        ];

        $cmd = null;
        $usedAssembler = null;

        foreach ($assemblers as $as) {
            if (shell_exec("which $as 2>/dev/null")) {
                $usedAssembler = $as;
                $cmd = "$as -o $objectFile $assemblyFile 2>&1";
                break;
            }
        }

        if ($cmd === null) {
            throw new Exception("No se encontró ensamblador (intenta: apt install binutils-aarch64-linux-gnu)");
        }

        echo BLUE . "🔧 Usando ensamblador:" . RESET . " $usedAssembler\n";
        echo BLUE . "⚙️  Comando:" . RESET . " $cmd\n";

        $output = shell_exec($cmd);
        $result['assemblerOutput'] = $output ?? '';

        if (!file_exists($objectFile)) {
            throw new Exception("El ensamblador no generó archivo .o\n" . ($output ?? ""));
        }

        $result['objectFile'] = $objectFile;
        $result['success'] = true;

        echo BLUE . "🔗 Objeto generado:" . RESET . " " . basename($objectFile) . "\n";
        echo BLUE . "📊 Tamaño:" . RESET . " " . filesize($objectFile) . " bytes\n";
        echo GREEN . "✅ Ensamblado exitoso" . RESET . "\n";

    } catch (Throwable $e) {
        $result['message'] = "Error: " . $e->getMessage();
        echo RED . "❌ " . $e->getMessage() . RESET . "\n";
    }

    return $result;
}

/**
 * FASE 4: Enlazar (linkar) y ejecutar
 */
function phase4_execute(string $objectFile): array
{
    echo "\n" . str_repeat("=", 100) . "\n";
    echo CYAN . "FASE 4: ENLAZADO Y EJECUCIÓN (.o → ejecutable)" . RESET . "\n";
    echo str_repeat("=", 100) . "\n";

    $result = [
        'success' => false,
        'executable' => '',
        'message' => '',
        'linkOutput' => '',
        'executionOutput' => '',
        'exitCode' => -1
    ];

    try {
        if (!file_exists($objectFile)) {
            throw new Exception("Archivo .o no encontrado: $objectFile");
        }

        $dir = dirname($objectFile);
        $base = pathinfo($objectFile, PATHINFO_FILENAME);
        $executable = "$dir/$base";

        // Para resolver referencias a printf se debe enlazar con runtime/libc.
        // Como el assembly define _start, primero se prueban variantes con -nostartfiles.
        $linkAttempts = [];
        if (command_exists('aarch64-linux-gnu-gcc')) {
            $linkAttempts[] = [
                'tool' => 'aarch64-linux-gnu-gcc',
                'cmd' => 'aarch64-linux-gnu-gcc -nostartfiles -static -o ' . escapeshellarg($executable) . ' ' . escapeshellarg($objectFile) . ' -lc',
            ];
            $linkAttempts[] = [
                'tool' => 'aarch64-linux-gnu-gcc',
                'cmd' => 'aarch64-linux-gnu-gcc -nostartfiles -o ' . escapeshellarg($executable) . ' ' . escapeshellarg($objectFile) . ' -lc',
            ];
            $linkAttempts[] = [
                'tool' => 'aarch64-linux-gnu-gcc',
                'cmd' => 'aarch64-linux-gnu-gcc -static -o ' . escapeshellarg($executable) . ' ' . escapeshellarg($objectFile),
            ];
            $linkAttempts[] = [
                'tool' => 'aarch64-linux-gnu-gcc',
                'cmd' => 'aarch64-linux-gnu-gcc -o ' . escapeshellarg($executable) . ' ' . escapeshellarg($objectFile),
            ];
        }
        if (command_exists('aarch64-linux-gnu-ld')) {
            $linkAttempts[] = [
                'tool' => 'aarch64-linux-gnu-ld',
                'cmd' => 'aarch64-linux-gnu-ld -static -o ' . escapeshellarg($executable) . ' ' . escapeshellarg($objectFile),
            ];
        }

        if (empty($linkAttempts)) {
            throw new Exception('No se encontró linker cruzado. Instala: gcc-aarch64-linux-gnu (recomendado) o binutils-aarch64-linux-gnu');
        }

        echo BLUE . "🔗 Linkeditando..." . RESET . "\n";

        $linked = false;
        $attemptLogs = [];
        foreach ($linkAttempts as $attempt) {
            echo BLUE . "⚙️  Probando:" . RESET . " {$attempt['cmd']}\n";
            $linkRun = run_shell_command($attempt['cmd']);

            $attemptLogs[] =
                "[{$attempt['tool']}] {$attempt['cmd']}\n" .
                "exit={$linkRun['exitCode']}\n" .
                ($linkRun['output'] !== '' ? $linkRun['output'] : '(sin salida)');

            if ($linkRun['exitCode'] === 0 && file_exists($executable)) {
                $linked = true;
                $result['linkOutput'] = implode("\n\n", $attemptLogs);
                echo BLUE . "🔧 Linker usado:" . RESET . " {$attempt['tool']}\n";
                break;
            }
        }

        if (!$linked) {
            $result['linkOutput'] = implode("\n\n", $attemptLogs);
            throw new Exception("No se pudo crear el ejecutable.\n" . $result['linkOutput']);
        }

        // Hacer ejecutable
        chmod($executable, 0755);

        $machine = strtolower(php_uname('m'));
        $isArmHost = str_contains($machine, 'aarch64') || str_contains($machine, 'arm64');

        if ($isArmHost) {
            $runCmd = escapeshellarg($executable);
        } else {
            if (!command_exists('qemu-aarch64')) {
                throw new Exception(
                    "El ejecutable es ARM64 y este host es '$machine'. " .
                    "Instala qemu-user (qemu-aarch64) para poder ejecutarlo."
                );
            }

            $qemuPrefix = is_dir('/usr/aarch64-linux-gnu')
                ? 'qemu-aarch64 -L /usr/aarch64-linux-gnu '
                : 'qemu-aarch64 ';
            $runCmd = $qemuPrefix . escapeshellarg($executable);
        }

        if (command_exists('timeout')) {
            $runCmd = 'timeout 8s ' . $runCmd;
        }

        echo BLUE . "🚀 Ejecutando:" . RESET . " $runCmd\n";

        $runResult = run_shell_command($runCmd);
        $result['executionOutput'] = $runResult['output'];
        $result['exitCode'] = $runResult['exitCode'];

        $result['executable'] = $executable;
        $result['success'] = $result['exitCode'] === 0;

        if (!empty($result['executionOutput'])) {
            echo BLUE . "📤 Salida del programa:" . RESET . "\n";
            echo $result['executionOutput'] . "\n";
        } else {
            echo YELLOW . "⚠️  El programa no produjo salida" . RESET . "\n";
        }

        echo BLUE . "📊 Código de salida:" . RESET . " " . $result['exitCode'] . "\n";
        if ($result['success']) {
            echo GREEN . "✅ Ejecución completada" . RESET . "\n";
        } elseif ($result['exitCode'] === 124) {
            echo RED . "❌ Tiempo límite excedido (timeout 8s). Posible bucle infinito." . RESET . "\n";
        } else {
            echo RED . "❌ El programa terminó con error" . RESET . "\n";
        }

    } catch (Throwable $e) {
        $result['message'] = "Error: " . $e->getMessage();
        echo RED . "❌ " . $e->getMessage() . RESET . "\n";
    }

    return $result;
}

// ============================================================================
// MAIN EXECUTION
// ============================================================================

echo "\n" . str_repeat("=", 100) . "\n";
echo BOLD . "COMPILADOR GOLAMPI - CICLO COMPLETO (4 FASES)" . RESET . "\n";
echo str_repeat("=", 100) . "\n";

$sourceFile = $argv[1] ?? __DIR__ . '/example.go';

if (!file_exists($sourceFile)) {
    echo RED . "❌ Archivo no encontrado: $sourceFile" . RESET . "\n";
    echo "\nUso: php test_phase4.php [archivo.go]\n";
    exit(1);
}

try {
    $structure = ensure_artifact_structure($sourceFile);
    echo BLUE . "📁 Carpeta de artefactos:" . RESET . " " . $structure['artifactRoot'] . "\n";
} catch (Throwable $e) {
    echo RED . "❌ " . $e->getMessage() . RESET . "\n";
    exit(1);
}

// Ejecutar las 4 fases
$phase1 = phase1_compile($sourceFile);
if (!$phase1['success']) {
    exit(1);
}

$phase2 = phase2_save_assembly($sourceFile, $phase1['assembly']);
if (!$phase2['success']) {
    exit(1);
}

$phase3 = phase3_assemble($phase2['assemblyFile']);
if (!$phase3['success']) {
    exit(1);
}

$phase4 = phase4_execute($phase3['objectFile']);

// Resumen final
echo "\n" . str_repeat("=", 100) . "\n";
echo BOLD . "RESUMEN FINAL" . RESET . "\n";
echo str_repeat("=", 100) . "\n";
echo "✓ Fase 1 (Compilación):  " . (GREEN . "✅ OK" . RESET) . "\n";
echo "✓ Fase 2 (Guardar .s):   " . (GREEN . "✅ OK" . RESET) . "\n";
echo "✓ Fase 3 (Ensamblado):   " . ($phase3['success'] ? GREEN . "✅ OK" . RESET : RED . "❌ FALLO" . RESET) . "\n";
echo "✓ Fase 4 (Ejecución):    " . ($phase4['success'] ? GREEN . "✅ OK" . RESET : RED . "❌ FALLO" . RESET) . "\n";
echo str_repeat("=", 100) . "\n\n";

exit($phase4['success'] ? 0 : 1);
