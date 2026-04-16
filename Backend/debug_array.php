#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = __DIR__;
require_once $root . '/vendor/autoload.php';

use Golampi\Compiler\CompilationHandler;

try {
    $handler = new CompilationHandler();
    $result = $handler->compile(<<<'GO'
package main

func main() {
    var a [5]int32
    a[0] = 42
}
GO);

    echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
    if (!$result['success']) {
        echo "Error message: " . $result['errors'][0]['description'] . "\n";
    }
} catch (\Throwable $e) {
    echo "Exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack:\n";
    echo $e->getTraceAsString() . "\n";
}
