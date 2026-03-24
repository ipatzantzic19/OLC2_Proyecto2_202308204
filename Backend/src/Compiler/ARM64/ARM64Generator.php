<?php

namespace Golampi\Compiler\ARM64;

use Golampi\Compiler\CompilationResult;
use Golampi\Compiler\ARM64\Traits\EmitterTrait;
use Golampi\Compiler\ARM64\Traits\StringPoolTrait;
use Golampi\Compiler\ARM64\Traits\DeclarationsTrait;
use Golampi\Compiler\ARM64\Traits\AssignmentsTrait;
use Golampi\Compiler\ARM64\Traits\ControlFlowTrait;
use Golampi\Compiler\ARM64\Traits\ExpressionsTrait;
use Golampi\Compiler\ARM64\Traits\LiteralsTrait;
use Golampi\Compiler\ARM64\Traits\FunctionCallTrait;
use Golampi\Compiler\ARM64\Traits\HelpersTrait;

require_once __DIR__ . '/../../../generated/GolampiVisitor.php';
require_once __DIR__ . '/../../../generated/GolampiBaseVisitor.php';

/**
 * ARM64Generator
 *
 * Generador de código ARM64 (AArch64) para el lenguaje Golampi.
 * Esta clase actúa como orquestador: declara el estado compartido y
 * delega toda la lógica de generación en traits especializados.
 *
 * Traits y responsabilidades:
 *
 *  EmitterTrait        emit(), label(), comment(), buildAssembly(),
 *                      pushStack(), emitBinaryOp(), newLabel()
 *
 *  StringPoolTrait     internString(), asmEscape()
 *
 *  DeclarationsTrait   visitVarDecl*, visitShortVarDecl, visitConstDecl,
 *                      prescanBlock/Node/Ids
 *
 *  AssignmentsTrait    visitSimpleAssignment, visitIncrement/Decrement
 *
 *  ControlFlowTrait    visitIf*, visitFor*, visitSwitch,
 *                      visitBreak/Continue/Return, visitForInit (public),
 *                      visitForPost (public)
 *
 *  ExpressionsTrait    visitExpression, visitLogical*, visitEquality,
 *                      visitRelational, visitAdditive, visitMultiplicative,
 *                      visitUnary*, visitGroupedExpression
 *
 *  LiteralsTrait       visitIntLiteral, visitFloatLiteral, visitRuneLiteral,
 *                      visitStringLiteral, visitTrueLiteral, visitFalseLiteral,
 *                      visitNilLiteral
 *
 *  FunctionCallTrait   visitFunctionCall, visitExpression/AddressArgument,
 *                      generateFmtPrintln, generatePrintValue
 *
 *  HelpersTrait        getTypeName, allocVar, storeDefault,
 *                      addSymbol, addError, visitIdentifier
 */
class ARM64Generator extends \GolampiBaseVisitor
{
    use EmitterTrait;
    use StringPoolTrait;
    use DeclarationsTrait;
    use AssignmentsTrait;
    use ControlFlowTrait;
    use ExpressionsTrait;
    use LiteralsTrait;
    use FunctionCallTrait;
    use HelpersTrait;

    // ── Secciones de ensamblador ──────────────────────────────────────────────
    protected array $dataLines = [];
    protected array $textLines = [];

    // ── Pool de strings ───────────────────────────────────────────────────────
    protected array $stringPool = [];
    protected int   $strIdx     = 0;

    // ── Contador de etiquetas ─────────────────────────────────────────────────
    protected int $labelIdx = 0;

    // ── Reportes ─────────────────────────────────────────────────────────────
    protected array $symbolTable = [];
    protected array $errors      = [];

    // ── Función actual ────────────────────────────────────────────────────────
    protected ?FunctionContext $func = null;

    // ── Stack break/continue ──────────────────────────────────────────────────
    protected array $loopStack = [];

    // ── Funciones de usuario registradas ─────────────────────────────────────
    protected array $userFunctions = [];

    // ═════════════════════════════════════════════════════════════════════════
    //  API PÚBLICA
    // ═════════════════════════════════════════════════════════════════════════

    public function generateFromTree($programCtx): CompilationResult
    {
        $start = microtime(true);
        $this->visit($programCtx);
        $elapsed = round((microtime(true) - $start) * 1000, 2) . 'ms';

        return new CompilationResult(
            $this->buildAssembly(),
            $this->errors,
            $this->symbolTable,
            $elapsed
        );
    }

    // ═════════════════════════════════════════════════════════════════════════
    //  PROGRAMA
    // ═════════════════════════════════════════════════════════════════════════

    public function visitProgram($ctx)
    {
        $mainDecl = null;

        // Pasada 1: registrar funciones (hoisting)
        for ($i = 0; $i < $ctx->getChildCount() - 1; $i++) {
            $child = $ctx->getChild($i);
            if (!($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext)) continue;

            $funcDecl = null;
            try {
                if (is_callable([$child, 'functionDeclaration'])) {
                    $funcDecl = $child->functionDeclaration();
                } elseif (is_callable([$child, 'ID']) && is_callable([$child, 'block'])) {
                    $funcDecl = $child;
                }
            } catch (\Throwable $e) {
                // Ignorar si no se puede llamar
            }

            if ($funcDecl !== null) {
                $name = $funcDecl->ID()->getText();
                if ($name === 'main') {
                    $mainDecl = $funcDecl;
                } else {
                    $this->userFunctions[$name] = $funcDecl;
                }
            }
        }

        if ($mainDecl === null) {
            $this->addError('Semántico', 'No se encontró la función main()', 0, 0);
            return null;
        }

        $this->generateFunction($mainDecl);
        foreach ($this->userFunctions as $decl) {
            $this->generateFunction($decl);
        }

        return null;
    }

    // ═════════════════════════════════════════════════════════════════════════
    //  GENERACIÓN DE FUNCIÓN
    // ═════════════════════════════════════════════════════════════════════════

    protected function generateFunction($funcDecl): void
    {
        $name = $funcDecl->ID()->getText();
        $line = $funcDecl->getStart()->getLine();
        $col  = $funcDecl->getStart()->getCharPositionInLine();

        $this->addSymbol($name, 'function', 'global', null, $line, $col);

        $this->func = new FunctionContext($name);

        // 1. Registrar parámetros PRIMERO para que tengan slots antes del prescan
        $params = $this->extractParams($funcDecl);
        foreach ($params as $p) {
            $this->func->allocLocal($p['name'], $p['type']);
            $this->addSymbol($p['name'], $p['type'], $name, 'param', $line, $col);
        }

        // 2. Prescanear variables locales del bloque
        if ($funcDecl->block()) {
            $this->prescanBlock($funcDecl->block());
        }

        $epilogueLabel             = '.epilogue_' . $name;
        $this->func->epilogueLabel = $epilogueLabel;
        $frameSize                 = $this->func->getFrameSize();

        // Prólogo
        $this->label($name);
        $this->comment("── función $name ──");
        $this->emit('stp x29, x30, [sp, #-16]!',  'guardar fp y lr');
        $this->emit('mov x29, sp',                  'frame pointer');
        if ($frameSize > 0) {
            $this->emit("sub sp, sp, #$frameSize",  "reservar $frameSize bytes");
        }

        // 3. Guardar parámetros del registro al stack (convención AArch64: x0..x7)
        foreach ($params as $idx => $p) {
            if ($idx >= 8) break;
            $offset = $this->func->getOffset($p['name']);
            $this->comment("param {$p['name']} (x$idx) → [fp-$offset]");
            $this->emit("str x$idx, [x29, #-$offset]");
        }

        // Cuerpo
        if ($funcDecl->block()) {
            $this->generateBlock($funcDecl->block());
        }

        // Epílogo
        $this->label($epilogueLabel);
        if ($frameSize > 0) {
            $this->emit("add sp, sp, #$frameSize",  'liberar locales');
        }
        $this->emit('ldp x29, x30, [sp], #16',     'restaurar fp y lr');
        if ($name === 'main') {
            $this->emit('mov x0, #0',               'exit code 0');
        }
        $this->emit('ret');
        $this->textLines[] = '';

        $this->func = null;
    }

    // ═════════════════════════════════════════════════════════════════════════
    //  BLOQUE
    // ═════════════════════════════════════════════════════════════════════════

    protected function generateBlock($blockCtx): void
    {
        for ($i = 1; $i < $blockCtx->getChildCount() - 1; $i++) {
            $child = $blockCtx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $this->visit($child);
            }
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    //  EXTRACCIÓN DE PARÁMETROS
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Extrae la lista de parámetros de una declaración de función.
     * Devuelve array de ['name' => string, 'type' => string].
     *
     * Soporta gramáticas con paramList() y también iteración directa
     * sobre los hijos del nodo funcDecl buscando contextos param.
     */
    private function extractParams($funcDecl): array
    {
        $params   = [];
        $paramList = null;

        // Intentar acceder al paramList usando try-catch
        try {
            if (is_callable([$funcDecl, 'parameterList'])) {
                $paramList = $funcDecl->parameterList();
            }
        } catch (\Throwable $e) {
            // Ignorar si no existe
        }

        if ($paramList === null) {
            return $params;
        }

        // Iterar sobre hijos del paramList: param (COMMA param)*
        for ($i = 0; $i < $paramList->getChildCount(); $i++) {
            $child = $paramList->getChild($i);

            // Saltar tokens (comas, paréntesis)
            if (!($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext)) {
                continue;
            }

            // Cada nodo param debe tener ID() y type()
            try {
                if (is_callable([$child, 'ID']) && is_callable([$child, 'type'])) {
                    $pName   = $child->ID()->getText();
                    $pType   = $this->getTypeName($child->type());
                    $params[] = ['name' => $pName, 'type' => $pType];
                }
            } catch (\Throwable $e) {
                // Ignorar parámetros con problemas
            }
        }

        return $params;
    }
}