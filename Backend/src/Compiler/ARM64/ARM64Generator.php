<?php

namespace Golampi\Compiler\ARM64;

use Golampi\Compiler\CompilationResult;
use Golampi\Compiler\ARM64\Traits\Emitter\EmitterHandler;
use Golampi\Compiler\ARM64\Traits\StringPool\StringPoolHandler;
use Golampi\Compiler\ARM64\Traits\FloatOps\FloatHandler;
use Golampi\Compiler\ARM64\Traits\StringOps\StringOpsHandler;
use Golampi\Compiler\ARM64\Traits\Declarations\DeclHandler;
use Golampi\Compiler\ARM64\Traits\Assignments\AssignmentsHandler;
use Golampi\Compiler\ARM64\Traits\ControlFlow\ControlFlowHandler;
use Golampi\Compiler\ARM64\Traits\Expressions\ExpressionsHandler;
use Golampi\Compiler\ARM64\Traits\Literals\LiteralsHandler;
use Golampi\Compiler\ARM64\Traits\FunctionCall\FunctionCallHandler;
use Golampi\Compiler\ARM64\Traits\Helpers\HelpersHandler;
use Golampi\Compiler\ARM64\Traits\Phases\GeneratorPhaseHandler;

require_once __DIR__ . '/../../../generated/GolampiVisitor.php';
require_once __DIR__ . '/../../../generated/GolampiBaseVisitor.php';

// ── Traits de nivel superior (orquestadores) ──────────────────────────────────
require_once __DIR__ . '/Traits/Emitter/EmitterHandler.php';
require_once __DIR__ . '/Traits/StringPool/StringPoolHandler.php';
require_once __DIR__ . '/Traits/FloatOps/FloatHandler.php';
require_once __DIR__ . '/Traits/StringOps/StringOpsHandler.php';
require_once __DIR__ . '/Traits/Literals/LiteralsHandler.php';
require_once __DIR__ . '/Traits/Assignments/AssignmentsHandler.php';
require_once __DIR__ . '/Traits/FunctionCall/FunctionCallHandler.php';
require_once __DIR__ . '/Traits/Helpers/HelpersHandler.php';

// ── Orquestadores con sub-traits (los require_once internos los cargan) ───────
require_once __DIR__ . '/Traits/ControlFlow/ControlFlowHandler.php';
require_once __DIR__ . '/Traits/Declarations/DeclHandler.php';
require_once __DIR__ . '/Traits/Expressions/ExpressionsHandler.php';

// ── Nuevo sistema de fases (separación arquitectónica) ──────────────────────
require_once __DIR__ . '/FunctionContext.php';
require_once __DIR__ . '/Traits/Phases/PrescanPhase.php';
require_once __DIR__ . '/Traits/Phases/GenerationPhase.php';
require_once __DIR__ . '/Traits/Phases/ProgramPhase.php';
require_once __DIR__ . '/Traits/Phases/GeneratorPhaseHandler.php';

// ── Módulo de asignación de registros (AHU Cap. 8-9) ────────────────────────
require_once __DIR__ . '/Traits/RegisterAllocation/LivenessAnalysis.php';
require_once __DIR__ . '/Traits/RegisterAllocation/InterferenceGraph.php';
require_once __DIR__ . '/Traits/RegisterAllocation/GraphColoring.php';
require_once __DIR__ . '/Traits/RegisterAllocation/RegisterAllocator.php';

class ARM64Generator extends \GolampiBaseVisitor
{
    // ── Traits de infraestructura ─────────────────────────────────────────
    use EmitterHandler;
    use StringPoolHandler;
    use FloatHandler;
    use StringOpsHandler;
    use LiteralsHandler;
    use AssignmentsHandler;
    use FunctionCallHandler;
    use HelpersHandler;

    // ── Traits orquestadores (cada uno importa sus sub-traits) ────────────
    use ControlFlowHandler;  // ForClassic + ForWhile + ForInfinite + If + Switch + Transfer
    use DeclHandler;         // Prescan + VarDecl + ShortVarDecl + ConstDecl
    use ExpressionsHandler;  // Entry + Logical + Comparisons + Arithmetic + Unary

    // ── Nuevo sistema de fases (organización arquitectónica por pasada) ───
    use GeneratorPhaseHandler;  // PrescanPhase + GenerationPhase + ProgramPhase

    // ── Asignación optimizada de registros (AHU Cap. 8-9) ─────────────────
    use \Golampi\Compiler\ARM64\Traits\RegisterAllocation\RegisterAllocator;

    // ═══════════════════════════════════════════════════════════════════════
    //  ESTADO DEL GENERADOR
    // ═══════════════════════════════════════════════════════════════════════

    // ── Secciones del assembly ────────────────────────────────────────────
    protected array $dataLines     = [];   // sección .data (strings, floats, buffers)
    protected array $textLines     = [];   // sección .text (instrucciones)
    protected array $postTextLines = [];   // helpers runtime (golampi_concat, etc.)

    // ── Pools de constantes ───────────────────────────────────────────────
    protected array $stringPool = [];      // raw_value → label
    protected int   $strIdx     = 0;

    protected array $floatPool  = [];      // bits_ieee754 → label
    protected int   $floatIdx   = 0;

    // ── Contador de etiquetas únicas ──────────────────────────────────────
    protected int $labelIdx = 0;

    // ── Reportes ─────────────────────────────────────────────────────────
    protected array $symbolTable = [];
    protected array $errors      = [];

    // ── Contexto de función actual ────────────────────────────────────────
    protected ?FunctionContext $func = null;

    // ── Frame size fijado durante prólogo (consistencia prologue/epilogue) ─
    protected ?int $activeFrameSize = null;

    // ── Pila de contexto de bucles (para break/continue) ──────────────────
    // Cada entrada: ['break' => label, 'continue' => label]
    protected array $loopStack = [];

    // ── Rastreo de comparaciones simples (para branch directo en IF) ───────
    // Estructura: ['isSimple' => bool, 'op' => string, 'lhsReg' => string, 'rhsReg' => string]
    // Permite IF/ControlFlow usar b.EQ, b.LT, etc en lugar de cbz
    protected array $lastComparison = ['isSimple' => false, 'op' => '', 'lhsReg' => '', 'rhsReg' => ''];

    // ── Captura del último valor literal evaluado ──────────────────────────
    // Se usa para registrar el valor inicial en la tabla de símbolos
    // Estructura: ['type' => string, 'value' => mixed]
    // Se resetea a null después de usarse en addSymbol()
    protected ?array $lastLiteralValue = null;

    // ── Rastreo de arrays usados en expresiones / inicializaciones ─────────
    protected ?string $lastArrayName = null;
    protected ?string $pendingArrayInitName = null;
    protected array $arrayParamBindings = [];

    // ── Funciones de usuario registradas (hoisting) ───────────────────────
    protected array $userFunctions = [];

    // ── Flags de helpers runtime ya emitidos ─────────────────────────────
    private bool $concatHelperEmitted = false;
    private bool $substrHelperEmitted = false;
    private bool $nowHelperEmitted    = false;

    // ── Helpers generados (para ProgramPhase) ────────────────────────────
    protected array $generatedHelpers = [];  // ['concat' => true, 'substr' => true, 'now' => true]

    // ═══════════════════════════════════════════════════════════════════════
    //  API PÚBLICA
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Punto de entrada principal del generador.
     * Usa el nuevo sistema de fases (PrescanPhase + GenerationPhase).
     */
    public function generateFromTree($programCtx): CompilationResult
    {
        $start = microtime(true);
        $result = $this->phaseCompileProgram($programCtx);
        $elapsed = round((microtime(true) - $start) * 1000, 2) . 'ms';

        // Asegurar que result tenga la propiedad elapsed correcta
        if ($result instanceof \Golampi\Compiler\CompilationResult) {
            // Retornar directamente el resultado que phaseCompileProgram retornó
            return $result;
        }

        // Fallback (no debería ocurrir)
        return new CompilationResult(
            $result->assembly ?? $this->buildAssembly(),
            $result->errors ?? $this->errors,
            $result->symbolTable ?? $this->symbolTable,
            $elapsed
        );
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  VISITA DEL PROGRAMA (hoisting de funciones)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * visitProgram — Punto de entrada para la compilación.
     * Ahora delega a phaseCompileProgram (ProgramPhase) que usa PrescanPhase + GenerationPhase.
     *
     * Flujo:
     *   1. phasePrescanGlobalFunctions: hoisting de funciones
     *   2. Para cada función: phaseGenerateFunction
     *      - phasePrescanBlock: registra variables/arrays
     *      - phaseGeneratePrologue: stp x29, x30; mov fp, sp; sub sp, sp, #FRAME_SIZE
     *      - phaseGenerateBlock: recorre AST y genera instrucciones
     *      - phaseGenerateEpilogue: restaura registros y ret
     *   3. phaseGenerateSymbolTable: crea tabla para depuración
     *   4. phaseGenerateHelpers: emite funciones auxiliares (concat, substr, now)
     *   5. buildAssembly: combina todo en output final
     */
    public function visitProgram($ctx)
    {
        return $this->phaseCompileProgram($ctx);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  GENERACIÓN DE FUNCIÓN
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Genera el código ARM64 completo de una función:
     *   1. Prólogo  (guardar fp/lr, reservar frame)
     *   2. Cuerpo   (instrucciones del bloque)
     *   3. Epílogo  (restaurar fp/lr, liberar frame, ret)
     *
     * El tamaño del frame (FRAME_SIZE) se calcula ANTES de generar el prólogo
     * mediante el prescan del bloque (PrescanTrait::prescanBlock).
     */
    protected function generateFunction($funcDecl): void
    {
        $name = $funcDecl->ID()->getText();
        $line = $funcDecl->getStart()->getLine();
        $col  = $funcDecl->getStart()->getCharPositionInLine();

        $this->addSymbol($name, 'function', 'global', null, $line, $col);
        $this->func = new FunctionContext($name);

        // ── Registrar parámetros ──────────────────────────────────────────
        $params = $this->extractParams($funcDecl);
        foreach ($params as $p) {
            $this->func->allocLocal($p['name'], $p['type'], true);
            // No agregar parámetros como símbolos separados - se reportan como parte de la función
            // $this->addSymbol($p['name'], $p['type'], $name, null, $line, $col);
        }

        // ── Pasada 1: prescan del bloque (calcular FRAME_SIZE) ────────────
        if ($funcDecl->block()) {
            $this->prescanBlock($funcDecl->block());
        }

        // Usar main directamente (compatible con aarch64-linux-gnu-gcc)
        $labelName                 = $name;
        $epilogueLabel             = '.epilogue_' . $labelName;
        $this->func->epilogueLabel = $epilogueLabel;
        $frameSize                 = $this->func->getFrameSize();

        // ── Prólogo ARM64 ────────────────────────────────────────────
        $this->textLines[] = '';
        if ($name === 'main') {
            $this->textLines[] = '.align 3';  // Alineación de función principal
        }
        $this->textLines[] = '.global ' . $labelName;
        $this->textLines[] = '';
        $this->label($labelName);
        $this->comment("-- funcion $name -- registro de activacion --");
        $this->emit('stp x29, x30, [sp, #-16]!', 'guardar fp (enlace control) y lr');
        $this->emit('mov x29, sp',                'establecer frame pointer');

        if ($frameSize > 0) {
            $this->emit("sub sp, sp, #$frameSize", "reservar $frameSize bytes (locales + params)");
        }

        // ── Copiar parámetros de registros al stack frame ─────────────────
        // Convención AArch64: int/bool/string/pointer en x0–x7, float32 en s0–s7
        // IMPORTANTE: int32 usa registros de 64-bit (x0-x7) per AArch64 standard
        $intReg   = 0;
        $floatReg = 0;
        foreach ($params as $p) {
            $offset = $this->func->getOffset($p['name']);
            if ($p['type'] === 'float32') {
                $reg = 's' . $floatReg++;
                $this->emit("str $reg, [x29, #-$offset]", "param {$p['name']} ($reg) → frame");
            } else {
                // int32/bool/rune/string/puntero → use 64-bit register x for all types
                if ($p['type'] === 'int32' || $p['type'] === 'bool' || $p['type'] === 'rune') {
                    $reg = 'x' . $intReg;
                    $this->emit("str $reg, [x29, #-$offset]", "param {$p['name']} ($reg int32) → frame");
                } else {
                    // puntero, string → 64-bit
                    $reg = 'x' . $intReg;
                    $this->emit("str $reg, [x29, #-$offset]", "param {$p['name']} ($reg ptr) → frame");
                }
                $intReg++;
            }
        }

        // ── Pasada 2: generación del cuerpo ──────────────────────────────
        if ($funcDecl->block()) {
            $this->generateBlock($funcDecl->block());
        }

        // ── Epílogo ARM64 ─────────────────────────────────────────────────
        $this->label($epilogueLabel);
        $this->comment("── epílogo $name ──");
        if ($frameSize > 0) {
            $this->emit("add sp, sp, #$frameSize", 'liberar espacio de locales');
        }
        $this->emit('ldp x29, x30, [sp], #16', 'restaurar fp y lr');
        if ($name === '_start' || $name === 'main') {
            $this->emit('mov x0, #0', 'exit code 0');
        }
        $this->emit('ret');

        $this->func = null;
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  GENERACIÓN DE BLOQUE
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Genera el código para todos los nodos hijo de un bloque { }.
     * Omite los tokens de apertura/cierre ({ y }) que son terminales.
     */
    protected function generateBlock($blockCtx): void
    {
        for ($i = 1; $i < $blockCtx->getChildCount() - 1; $i++) {
            $child = $blockCtx->getChild($i);
            if ($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext) {
                $this->visit($child);
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  EXTRACCIÓN DE METADATOS DE FUNCIONES
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Extrae los parámetros de una declaración de función.
     * Soporta parámetros normales (ID type) y puntero (* ID type).
     *
     * @return array  [['name'=>string, 'type'=>string, 'is_pointer'=>bool]]
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
                    $pName     = $child->ID()->getText();
                    $pType     = $this->getTypeName($child->type());
                    $isPointer = str_starts_with(get_class($child), 'Context\\PointerParameter');
                    $params[]  = ['name' => $pName, 'type' => $pType, 'is_pointer' => $isPointer];
                }
            } catch (\Throwable $e) {}
        }

        return $params;
    }

    /**
     * Extrae el nodo de declaración de función desde un nodo declaration.
     * Maneja ambas variantes: FuncDeclSingleReturn y FuncDeclMultiReturn.
     */
    private function extractFuncDecl($child): ?object
    {
        try {
            if (is_callable([$child, 'functionDeclaration'])) {
                $fd = $child->functionDeclaration();
                if ($fd !== null) return $fd;
            }
            // Nodo que ya ES una declaración de función directamente
            if (is_callable([$child, 'ID']) && is_callable([$child, 'block'])) {
                return $child;
            }
        } catch (\Throwable $e) {}
        return null;
    }
}