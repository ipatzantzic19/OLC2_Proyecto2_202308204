#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = __DIR__;
require_once $root . '/vendor/autoload.php';
require_once $root . '/generated/GolampiLexer.php';
require_once $root . '/generated/GolampiParser.php';

use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\CommonTokenStream;

$code = <<<'GO'
package main

func main() {
    var a [5]int32
    a[0] = 42
}
GO;

$inputStream = InputStream::fromString($code);
$lexer = new GolampiLexer($inputStream);
$stream = new CommonTokenStream($lexer);
$parser = new GolampiParser($stream);
$tree = $parser->program();

echo "Program context: " . get_class($tree) . "\n";
echo "Children count: " . $tree->getChildCount() . "\n";

for ($i = 0; $i < $tree->getChildCount(); $i++) {
    $child = $tree->getChild($i);
    if (!($child instanceof \TerminalNode)) {
        $fullClass = get_class($child);
        $className = substr($fullClass, strrpos($fullClass, '\\') + 1);
        echo "[$i] $className\n";
        
        // Si es Declaration, ver sus hijos
        if ($className === 'DeclarationContext') {
            for ($j = 0; $j < $child->getChildCount(); $j++) {
                $subChild = $child->getChild($j);
                if (!($subChild instanceof \TerminalNode)) {
                    $subFullClass = get_class($subChild);
                    $subClassName = substr($subFullClass, strrpos($subFullClass, '\\') + 1);
                    echo "  [$j] $subClassName\n";
                    
                    // Si es FuncDecl, ver qué métodos tiene
                    if (strpos($subClassName, 'Func') !== false) {
                        echo "      ID? " . (is_callable([$subChild, 'ID']) ? "YES" : "NO") . "\n";
                        echo "      block? " . (is_callable([$subChild, 'block']) ? "YES" : "NO") . "\n";
                        echo "      functionDeclaration? " . (is_callable([$subChild, 'functionDeclaration']) ? "YES" : "NO") . "\n";
                        
                        if (is_callable([$subChild, 'ID'])) {
                            $id = $subChild->ID();
                            echo "      ID value: " . ($id ? $id->getText() : "null") . "\n";
                        }
                    }
                }
            }
        }
    }
}
