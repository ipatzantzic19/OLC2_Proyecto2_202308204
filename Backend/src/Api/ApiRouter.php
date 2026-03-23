<?php

namespace Golampi\Api;

use Golampi\Compiler\CompilationHandler;

/**
 * Router API REST para Golampi IDE
 *
 * Endpoints del INTÉRPRETE (Proyecto 1 - se mantienen):
 *   POST /api/execute          → ejecuta código con el intérprete
 *   GET  /api/last-errors      → errores de la última ejecución
 *   GET  /api/last-symbols     → tabla de símbolos de la última ejecución
 *
 * Endpoints del COMPILADOR (Proyecto 2):
 *   POST /api/compile          → compila a ARM64 y devuelve el assembly
 *   GET  /api/last-assembly    → código ARM64 de la última compilación
 *   GET  /api/compile-errors   → errores de la última compilación
 *   GET  /api/compile-symbols  → tabla de símbolos de la última compilación
 *   GET  /api/download-asm     → descarga el archivo .s generado
 */
class ApiRouter
{
    private array $routes = [];
    private ExecutionHandler $executionHandler;
    private CompilationHandler $compilationHandler;

    public function __construct()
    {
        $this->executionHandler   = new ExecutionHandler();
        $this->compilationHandler = new CompilationHandler();
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        // ── Intérprete (Proyecto 1) ──────────────────────────────────────
        $this->routes['POST']['/api/execute']       = [$this, 'handleExecute'];
        $this->routes['GET']['/api/last-errors']    = [$this, 'handleLastErrors'];
        $this->routes['GET']['/api/last-symbols']   = [$this, 'handleLastSymbols'];

        // ── Compilador (Proyecto 2) ───────────────────────────────────────
        $this->routes['POST']['/api/compile']           = [$this, 'handleCompile'];
        $this->routes['GET']['/api/last-assembly']      = [$this, 'handleLastAssembly'];
        $this->routes['GET']['/api/compile-errors']     = [$this, 'handleCompileErrors'];
        $this->routes['GET']['/api/compile-symbols']    = [$this, 'handleCompileSymbols'];
        $this->routes['GET']['/api/download-asm']       = [$this, 'handleDownloadAsm'];
    }

    public function handle(string $method, string $path, array $body = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if (isset($this->routes[$method][$path])) {
            try {
                $response = call_user_func($this->routes[$method][$path], $body);

                // handleDownloadAsm envía el archivo y ya terminó
                if ($response !== null) {
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                }
            } catch (\Throwable $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error'   => 'Error interno del servidor: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error'   => "Endpoint no encontrado: $method $path"
            ]);
        }
    }

    // ═════════════════════════════════════════════════════════════════════
    //  INTÉRPRETE
    // ═════════════════════════════════════════════════════════════════════

    private function handleExecute(array $body): array
    {
        if (!isset($body['code']) || empty(trim($body['code']))) {
            return [
                'success'     => false,
                'error'       => 'El código no puede estar vacío',
                'output'      => [],
                'errors'      => [],
                'symbolTable' => []
            ];
        }
        return $this->executionHandler->execute($body['code']);
    }

    private function handleLastErrors(array $body = []): array
    {
        $path   = sys_get_temp_dir() . '/golampi_last_errors.json';
        $errors = [];
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) $errors = $decoded;
        }
        return ['success' => true, 'errors' => $errors, 'errorCount' => count($errors)];
    }

    private function handleLastSymbols(array $body = []): array
    {
        $path    = sys_get_temp_dir() . '/golampi_last_symbols.json';
        $symbols = [];
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) $symbols = $decoded;
        }
        return ['success' => true, 'symbolTable' => $symbols, 'symbolCount' => count($symbols)];
    }

    // ═════════════════════════════════════════════════════════════════════
    //  COMPILADOR
    // ═════════════════════════════════════════════════════════════════════

    /**
     * POST /api/compile
     * Compila código Golampi a ARM64 y devuelve el assembly + reportes.
     */
    private function handleCompile(array $body): array
    {
        if (!isset($body['code']) || empty(trim($body['code']))) {
            return [
                'success'      => false,
                'error'        => 'El código fuente no puede estar vacío',
                'assembly'     => '',
                'errors'       => [],
                'symbolTable'  => [],
                'executionTime'=> '0ms',
            ];
        }
        return $this->compilationHandler->compile($body['code']);
    }

    /**
     * GET /api/last-assembly
     * Devuelve el código ARM64 de la última compilación.
     */
    private function handleLastAssembly(array $body = []): array
    {
        $path     = sys_get_temp_dir() . '/golampi_last_assembly.s';
        $assembly = file_exists($path) ? file_get_contents($path) : '';
        return ['success' => true, 'assembly' => $assembly];
    }

    /**
     * GET /api/compile-errors
     * Devuelve los errores de la última compilación.
     */
    private function handleCompileErrors(array $body = []): array
    {
        $path   = sys_get_temp_dir() . '/golampi_last_compile_errors.json';
        $errors = [];
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) $errors = $decoded;
        }
        return ['success' => true, 'errors' => $errors, 'errorCount' => count($errors)];
    }

    /**
     * GET /api/compile-symbols
     * Devuelve la tabla de símbolos de la última compilación.
     */
    private function handleCompileSymbols(array $body = []): array
    {
        $path    = sys_get_temp_dir() . '/golampi_last_compile_symbols.json';
        $symbols = [];
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) $symbols = $decoded;
        }
        return ['success' => true, 'symbolTable' => $symbols, 'symbolCount' => count($symbols)];
    }

    /**
     * GET /api/download-asm
     * Descarga el archivo .s generado (Content-Disposition: attachment).
     */
    private function handleDownloadAsm(array $body = []): ?array
    {
        $path = sys_get_temp_dir() . '/golampi_last_assembly.s';

        if (!file_exists($path) || filesize($path) === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'No hay código ARM64 disponible']);
            return null;
        }

        $filename = 'program.s';
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}