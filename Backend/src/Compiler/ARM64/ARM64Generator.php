<?php

namespace Golampi\Compiler\ARM64;

use Golampi\Compiler\CompilationResult;
use Golampi\Compiler\ARM64\Traits\EmitterTrait;
use Golampi\Compiler\ARM64\Traits\StringPoolTrait;
use Golampi\Compiler\ARM64\Traits\FloatOpsTrait;
use Golampi\Compiler\ARM64\Traits\StringOpsTrait;
use Golampi\Compiler\ARM64\Traits\DeclarationsTrait;
use Golampi\Compiler\ARM64\Traits\AssignmentsTrait;
use Golampi\Compiler\ARM64\Traits\ControlFlowTrait;
use Golampi\Compiler\ARM64\Traits\ExpressionsTrait;
use Golampi\Compiler\ARM64\Traits\LiteralsTrait;
use Golampi\Compiler\ARM64\Traits\FunctionCallTrait;
use Golampi\Compiler\ARM64\Traits\HelpersTrait;

require_once __DIR__ . '/../../../generated/GolampiVisitor.php';
require_once __DIR__ . '/../../../generated/GolampiBaseVisitor.php';

// Carga defensiva de traits para entornos con autoload desactualizado.
require_once __DIR__ . '/Traits/EmitterTrait.php';
require_once __DIR__ . '/Traits/StringPoolTrait.php';
require_once __DIR__ . '/Traits/FloatOpsTrait.php';
require_once __DIR__ . '/Traits/StringOpsTrait.php';
require_once __DIR__ . '/Traits/DeclarationsTrait.php';
require_once __DIR__ . '/Traits/AssignmentsTrait.php';
require_once __DIR__ . '/Traits/ControlFlowTrait.php';
require_once __DIR__ . '/Traits/ExpressionsTrait.php';
require_once __DIR__ . '/Traits/LiteralsTrait.php';
require_once __DIR__ . '/Traits/FunctionCallTrait.php';
require_once __DIR__ . '/Traits/HelpersTrait.php';

/**
 * ARM64Generator — Fase 2
 *
 * Generador de código ARM64 (AArch64) para el lenguaje Golampi.
 * Orquestador principal: declara el estado compartido y delega en traits.
 *
 * Traits y responsabilidades:
 *  EmitterTrait        emit(), label(), comment(), buildAssembly()
 *  StringPoolTrait     internString(), asmEscape()
 *  FloatOpsTrait       internFloat(), emitFloat*(), pushFloatStack()  ← NUEVO Fase 2
 *  StringOpsTrait      emitStringConcat(), emitStrlen(), emitSubstr()  ← NUEVO Fase 2
 *  DeclarationsTrait   visitVarDecl*, visitShortVarDecl, visitConstDecl
 *  AssignmentsTrait    visitSimpleAssignment, visitIncrement/Decrement
 *  ControlFlowTrait    visitIf*, visitFor*, visitSwitch, visitBreak/Continue/Return
 *  ExpressionsTrait    operadores binarios (int y float), unarios
 *  LiteralsTrait       int32, float32 (SIMD), rune, string, bool, nil
 *  FunctionCallTrait   fmt.Println, funciones usuario (multi-param), builtins
 *  HelpersTrait        tipos, allocVar, storeDefault, addSymbol, visitIdentifier
 *
 * Registro de activación por función (conforme a Aho et al.):
 *  [fp + 0]  x29 guardado (enlace de control)
 *  [fp + 8]  x30 guardado (dirección de retorno)
 *  [fp - 8]  param 0 / local 0
 *  [fp - 16] param 1 / local 1
 *  ...
 *  [fp - N]  último local / array base
 */
class ARM64Generator extends \GolampiBaseVisitor
{
    use EmitterTrait;
    use StringPoolTrait;
    use FloatOpsTrait;      // ← Fase 2
    use StringOpsTrait;     // ← Fase 2
    use DeclarationsTrait;
    use AssignmentsTrait;
    use ControlFlowTrait;
    use ExpressionsTrait;
    use LiteralsTrait;
    use FunctionCallTrait;
    use HelpersTrait;

    // ── Secciones ─────────────────────────────────────────────────────────────
    protected array $dataLines = [];
    protected array $textLines = [];

    // ── Pool de strings ───────────────────────────────────────────────────────
    protected array $stringPool = [];
    protected int   $strIdx     = 0;

    // ── Pool de floats (Fase 2) ───────────────────────────────────────────────
    protected array $floatPool = [];
    protected int   $floatIdx  = 0;

    // ── Helpers runtime (Fase 2) ──────────────────────────────────────────────
    protected array $postTextLines = [];

    // ── Contador de etiquetas ─────────────────────────────────────────────────
    protected int $labelIdx = 0;

    // ── Reportes ─────────────────────────────────────────────────────────────
    protected array $symbolTable = [];
    protected array $errors      = [];

    // ── Función actual ────────────────────────────────────────────────────────
    protected ?FunctionContext $func = null;

    // ── Stack break/continue ──────────────────────────────────────────────────
    protected array $loopStack = [];

    // ── Funciones de usuario ──────────────────────────────────────────────────
    protected array $userFunctions = [];

    // ── Helpers de strings runtime emitidos ───────────────────────────────────
    private bool $concatHelperEmitted = false;
    private bool $substrHelperEmitted = false;
    private bool $nowHelperEmitted    = false;

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

        // Pasada 1: registro de funciones (hoisting)
        for ($i = 0; $i < $ctx->getChildCount() - 1; $i++) {
            $child = $ctx->getChild($i);
            if (!($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext)) continue;

            $funcDecl = $this->extractFuncDecl($child);
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

        // Generar main primero, luego las funciones de usuario
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

        // 1. Registrar parámetros con tipos correctos
        $params = $this->extractParams($funcDecl);
        foreach ($params as $p) {
            $this->func->allocLocal($p['name'], $p['type'], true);
            $this->addSymbol($p['name'], $p['type'], $name, 'param', $line, $col);
        }

        // 2. Prescanear variables locales del bloque
        if ($funcDecl->block()) {
            $this->prescanBlock($funcDecl->block());
        }

        $epilogueLabel             = '.epilogue_' . $name;
        $this->func->epilogueLabel = $epilogueLabel;
        $frameSize                 = $this->func->getFrameSize();

        // ── Prólogo ───────────────────────────────────────────────────────
        $this->textLines[] = '';
        $this->label($name);
        $this->comment("── función $name ── (registro de activación)");
        $this->emit('stp x29, x30, [sp, #-16]!',   'guardar fp (enlace control) y lr');
        $this->emit('mov x29, sp',                   'establecer frame pointer');

        if ($frameSize > 0) {
            $this->emit("sub sp, sp, #$frameSize",   "reservar $frameSize bytes (locales + params)");
        }

        // 3. Copiar parámetros de registros al stack frame
        // Convención AArch64: int/bool/string en x0–x7, float32 en s0–s7
        $intParamIdx   = 0;
        $floatParamIdx = 0;
        foreach ($params as $p) {
            $offset = $this->func->getOffset($p['name']);
            if ($p['type'] === 'float32') {
                $reg = 's' . $floatParamIdx++;
                $this->emit("str $reg, [x29, #-$offset]",  "param {$p['name']} ($reg) → frame");
            } else {
                $reg = 'x' . $intParamIdx++;
                $this->emit("str $reg, [x29, #-$offset]",  "param {$p['name']} ($reg) → frame");
            }
        }

        // ── Cuerpo ────────────────────────────────────────────────────────
        if ($funcDecl->block()) {
            $this->generateBlock($funcDecl->block());
        }

        // ── Epílogo ───────────────────────────────────────────────────────
        $this->label($epilogueLabel);
        $this->comment("── epílogo $name ──");
        if ($frameSize > 0) {
            $this->emit("add sp, sp, #$frameSize",   'liberar locales');
        }
        $this->emit('ldp x29, x30, [sp], #16',       'restaurar fp y lr');
        if ($name === 'main') {
            $this->emit('mov x0, #0',                'exit code 0');
        }
        $this->emit('ret');

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
     * Extrae parámetros de una declaración de función.
     * Retorna array de ['name'=>string, 'type'=>string, 'is_pointer'=>bool].
     */
    private function extractParams($funcDecl): array
    {
        $params    = [];
        $paramList = null;

        try {
            if (is_callable([$funcDecl, 'parameterList'])) {
                $paramList = $funcDecl->parameterList();
            }
        } catch (\Throwable $e) {}

        if ($paramList === null) return $params;

        for ($i = 0; $i < $paramList->getChildCount(); $i++) {
            $child = $paramList->getChild($i);
            if (!($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext)) continue;

            try {
                if (is_callable([$child, 'ID']) && is_callable([$child, 'type'])) {
                    $pName      = $child->ID()->getText();
                    $pType      = $this->getTypeName($child->type());
                    $isPointer  = str_starts_with(get_class($child), 'Context\\PointerParameter');
                    $params[]   = ['name' => $pName, 'type' => $pType, 'is_pointer' => $isPointer];
                }
            } catch (\Throwable $e) {}
        }

        return $params;
    }

    /**
     * Extrae la declaración de función de un nodo declaration del árbol.
     */
    private function extractFuncDecl($child): ?object
    {
        try {
            if (is_callable([$child, 'functionDeclaration'])) {
                $fd = $child->functionDeclaration();
                if ($fd !== null) return $fd;
            }
            if (is_callable([$child, 'ID']) && is_callable([$child, 'block'])) {
                return $child;
            }
        } catch (\Throwable $e) {}
        return null;
    }
}