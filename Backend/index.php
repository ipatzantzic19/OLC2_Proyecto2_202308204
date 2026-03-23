<?php

declare(strict_types=1);

// ── Autoloader ──────────────────────────────────────────────────────────────
require_once __DIR__ . '/vendor/autoload.php';

use Golampi\Api\ApiRouter;

// ── CORS preflight ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

// ── Parsear ruta y método ────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Leer body JSON
$body = [];
if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $body = $decoded;
        }
    }
}

// ── Dispatch ─────────────────────────────────────────────────────────────────
try {
    $router = new ApiRouter();
    $router->handle($method, $uri, $body);
} catch (\Throwable $e) {
    // Capturar cualquier error fatal y devolver JSON en vez de HTML
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        http_response_code(500);
    }
    echo json_encode([
        'success'      => false,
        'assembly'     => '',
        'errors'       => [[
            'id'          => 1,
            'type'        => 'Fatal',
            'description' => '[PHP] ' . $e->getMessage()
                           . ' en ' . basename($e->getFile())
                           . ':' . $e->getLine(),
            'line'        => 0,
            'column'      => 0,
        ]],
        'symbolTable'  => [],
        'programOutput'=> '',
        'executionTime'=> '0ms',
        'errorCount'   => 1,
        'symbolCount'  => 0,
    ], JSON_UNESCAPED_UNICODE);
}