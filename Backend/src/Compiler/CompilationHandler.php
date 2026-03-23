<?php

namespace Golampi\Compiler;

use Golampi\Compiler\ARM64\ARM64Generator;
use Golampi\Traits\ErrorHandler;
use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\Error\Listeners\BaseErrorListener;

require_once __DIR__ . '/../../generated/GolampiLexer.php';
require_once __DIR__ . '/../../generated/GolampiParser.php';

/**
 * Error listener para el compilador (igual estructura que el intérprete)
 */
class CompilerErrorListener extends BaseErrorListener
{
    use ErrorHandler;

    public function syntaxError(
        \Antlr\Antlr4\Runtime\Recognizer $recognizer,
        ?object $offendingSymbol,
        int $line,
        int $charPositionInLine,
        string $msg,
        ?\Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException $exception
    ): void {
        $this->addAntlrError($recognizer, $msg, $line, $charPositionInLine);
    }
}

/**
 * Handler del endpoint POST /api/compile
 *
 * Pipeline:
 *   Código fuente Golampi
 *     → ANTLR4 Lexer + Parser
 *     → ARM64Generator (Visitor)
 *     → CompilationResult (assembly + errores + tabla de símbolos)
 */
class CompilationHandler
{
    public function compile(string $code): array
    {
        $startTime = microtime(true);

        if (trim($code) === '') {
            return [
                'success'      => false,
                'assembly'     => '',
                'errors'       => [[
                    'type'        => 'Semántico',
                    'description' => 'El código fuente está vacío',
                    'line'        => 0,
                    'column'      => 0,
                ]],
                'symbolTable'  => [],
                'programOutput'=> '',
                'executionTime'=> '0ms',
                'timestamp'    => date('Y-m-d H:i:s'),
                'errorCount'   => 1,
                'symbolCount'  => 0,
            ];
        }

        try {
            // ── 1. Análisis léxico ────────────────────────────────────────
            $input = InputStream::fromString($code);
            $lexer = new \GolampiLexer($input);

            $lexerListener = new CompilerErrorListener();
            $lexer->removeErrorListeners();
            $lexer->addErrorListener($lexerListener);

            // ── 2. Análisis sintáctico ────────────────────────────────────
            $tokens = new CommonTokenStream($lexer);
            $parser = new \GolampiParser($tokens);

            $parserListener = new CompilerErrorListener();
            $parser->removeErrorListeners();
            $parser->addErrorListener($parserListener);

            $tree = $parser->program();

            // Recolectar errores léxicos y sintácticos
            $frontendErrors = array_merge(
                $lexerListener->getErrors(),
                $parserListener->getErrors()
            );

            // Si hay errores sintácticos graves, devolver sin generar código
            $hasFatalSyntax = count($parserListener->getErrors()) > 0;

            // ── 3. Generación de código ARM64 ─────────────────────────────
            $generator = new ARM64Generator();
            $result    = $generator->generateFromTree($tree);

            // Combinar errores front-end con errores semánticos del generador
            $allErrors = array_merge($frontendErrors, $result->errors);

            // Numerar errores para la tabla del reporte
            $numberedErrors = array_values(array_map(
                function (array $err, int $idx): array {
                    return array_merge(['id' => $idx + 1], $err);
                },
                $allErrors,
                array_keys($allErrors)
            ));

            $elapsed = round((microtime(true) - $startTime) * 1000, 2) . 'ms';

            // Si hubo errores fatales de sintaxis, no mostrar assembly parcial
            $assembly = ($hasFatalSyntax && empty($result->assembly))
                ? ''
                : $result->assembly;

            // Persistir en /tmp para endpoints GET reutilizables
            $tmp = sys_get_temp_dir();
            @file_put_contents(
                $tmp . '/golampi_last_compile_errors.json',
                json_encode($numberedErrors, JSON_UNESCAPED_UNICODE)
            );
            @file_put_contents(
                $tmp . '/golampi_last_compile_symbols.json',
                json_encode($result->symbolTable, JSON_UNESCAPED_UNICODE)
            );
            @file_put_contents(
                $tmp . '/golampi_last_assembly.s',
                $assembly
            );

            return [
                'success'      => empty($allErrors),
                'assembly'     => $assembly,
                'errors'       => $numberedErrors,
                'symbolTable'  => $result->symbolTable,
                'programOutput'=> $result->programOutput,
                'executionTime'=> $elapsed,
                'timestamp'    => date('Y-m-d H:i:s'),
                'errorCount'   => count($numberedErrors),
                'symbolCount'  => count($result->symbolTable),
            ];

        } catch (\Throwable $e) {
            $elapsed = round((microtime(true) - $startTime) * 1000, 2) . 'ms';

            return [
                'success'      => false,
                'assembly'     => '',
                'errors'       => [[
                    'id'          => 1,
                    'type'        => 'Fatal',
                    'description' => 'Error interno del compilador: ' . $e->getMessage(),
                    'line'        => 0,
                    'column'      => 0,
                ]],
                'symbolTable'  => [],
                'programOutput'=> '',
                'executionTime'=> $elapsed,
                'timestamp'    => date('Y-m-d H:i:s'),
                'errorCount'   => 1,
                'symbolCount'  => 0,
            ];
        }
    }
}