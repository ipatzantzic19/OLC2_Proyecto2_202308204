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
require_once __DIR__ . '/Traits/Phases/PrescanPhase.php';
require_once __DIR__ . '/Traits/Phases/GenerationPhase.php';
require_once __DIR__ . '/Traits/Phases/ProgramPhase.php';
require_once __DIR__ . '/Traits/Phases/GeneratorPhaseHandler.php';

/**
 * ARM64Generator — Generador de código ensamblador ARM64 (AArch64)
 *
 * Clase principal del backend del compilador Golampi.
 * Extiende GolampiBaseVisitor e implementa el patrón Visitor sobre el
 * árbol sintáctico generado por ANTLR4.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  ARQUITECTURA DE TRAITS (organización modular)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Todos los traits están organizados como orquestadores con sub-traits:
 *
 *   EmitterHandler ← orquesta:
 *     Emitter/InstructionEmitter  → emit(), label(), comment(), newLabel(), pushStack(), emitBinaryOp()
 *     Emitter/AssemblyBuilder     → buildAssembly()
 *
 *   StringPoolHandler ← orquesta:
 *     StringPool/PoolTrait        → internString(), asmEscape()
 *
 *   FloatHandler ← orquesta:
 *     FloatOps/FloatArithmetic    → fadd, fsub, fmul, fdiv con promoción de tipos
 *     FloatOps/FloatComparison    → comparaciones de floats (== != > >= < <=)
 *     FloatOps/FloatPool          → internFloat() para literales float
 *
 *   LiteralsHandler ← orquesta:
 *     Literals/IntLiteral         → visitIntLiteral()
 *     Literals/FloatLiteral       → visitFloatLiteral()
 *     Literals/RuneLiteral        → visitRune()
 *     Literals/StringLiteral      → visitStringLiteral()
 *     Literals/ScalarLiteral      → visitScalarLiteral()
 *
 *   AssignmentsHandler ← orquesta:
 *     Assignments/SimpleAssignment       → asignaciones simples (x = expr)
 *     Assignments/ArrayAssignment        → asignaciones a arrays (a[i] = expr)
 *     Assignments/PointerAssignment      → asignaciones a punteros (*p = expr)
 *     Assignments/IncrementDecrement     → ++, -- (pre y post)
 *
 *   StringOpsHandler ← orquesta:
 *     StringOps/ConcatHelper      → emitStringConcat() + golampi_concat helper
 *     StringOps/StringLenHelper   → emitStrlen()
 *     StringOps/SubstrHelper      → emitSubstr() + golampi_substr helper
 *     StringOps/NowHelper         → emitNow() + golampi_now helper
 *     StringOps/TypeOfHelper      → emitTypeOf()
 *
 *   FunctionCallHandler ← orquesta:
 *     FunctionCall/PrintlnCall    → fmt.Println()
 *     FunctionCall/BuiltinCall    → funciones built-in (len, append, etc.)
 *     FunctionCall/UserFunctionCall → llamadas a funciones usuario (con multi-retorno)
 *
 *   HelpersHandler ← orquesta:
 *     Helpers/SymbolManager       → allocVar(), addSymbol(), visitIdentifier()
 *     Helpers/TypeResolver        → getTypeName(), tipo de expresiones
 *     Helpers/FrameAllocator      → cálculo de frame size
 *     Helpers/IdentifierVisitor   → resolución de identificadores
 *
 *   ControlFlowHandler ← orquesta:
 *     ControlFlow/ForClassic      → for init ; cond ; post { }
 *     ControlFlow/ForWhile        → for cond { }
 *     ControlFlow/ForInfinite     → for { }
 *     ControlFlow/Condition       → if / else if / else
 *     ControlFlow/SwitchCase      → switch / case / default
 *     ControlFlow/Transfer        → break / continue / return
 *
 *   DeclHandler ← orquesta:
 *     Declarations/Prescan        → pasada 1: registrar variables
 *     Declarations/VarDecl        → var x T  /  var x T = expr
 *     Declarations/ShortVarDecl   → x := expr (tipo inferido)
 *     Declarations/ConstDecl      → const x T = expr
 *
 *   ExpressionsHandler ← orquesta:
 *     Expressions/ExpressionEntry → visitExpression (punto de entrada)
 *     Expressions/LogicalOps      → ||, && con cortocircuito
 *     Expressions/Comparisons     → ==, !=, >, >=, <, <=
 *     Expressions/ArithmeticOps   → +, -, *, /, % con promoción tipos
 *     Expressions/UnaryOps        → -, !, &, *, (expr), array access
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  REGISTRO DE ACTIVACIÓN (stack frame por función)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Conforme a Aho, Lam, Sethi — "Compiladores: Principios, Técnicas y Herramientas"
 * y a la convención de llamadas AArch64 (AAPCS64):
 *
 *   [dirección alta]
 *   ┌───────────────────────┐ ← sp_original (antes del stp)
 *   │  x29 (FP guardado)    │ ← [fp + 0]   → enlace de control
 *   │  x30 (LR guardado)    │ ← [fp + 8]   → dirección de retorno
 *   ├───────────────────────┤ ← x29 = fp (frame pointer)
 *   │  param 0 / local 0   │ ← [fp - 8]
 *   │  param 1 / local 1   │ ← [fp - 16]
 *   │        ...            │
 *   │  último local / array │ ← [fp - N]
 *   └───────────────────────┘ ← sp (fp - FRAME_SIZE)
 *   [dirección baja]
 *
 * Convención de registros:
 *   x0–x7   → argumentos enteros / punteros / valor de retorno
 *   s0–s7   → argumentos float32 / valor de retorno float
 *   x19     → callee-saved temporal para switch (valor de la expresión)
 *   x29     → frame pointer (fp)
 *   x30     → link register (lr / dirección de retorno)
 *   sp      → stack pointer
 *
 * ═══════════════════════════════════════════════════════════════════════════
 *  PROCESO DE COMPILACIÓN (dos pasadas por función)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *   Pasada 1 — Prescan (PrescanTrait):
 *     Recorre el AST del bloque y registra todas las variables en
 *     FunctionContext para calcular FRAME_SIZE antes de generar código.
 *
 *   Pasada 2 — Generación (todos los demás traits):
 *     Genera las instrucciones ARM64 recorriendo el AST con el Visitor.
 */
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

    // ── Pila de contexto de bucles (para break/continue) ──────────────────
    // Cada entrada: ['break' => label, 'continue' => label]
    protected array $loopStack = [];

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
     * Visita el árbol del programa y retorna el resultado de compilación.
     */
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

    // ═══════════════════════════════════════════════════════════════════════
    //  VISITA DEL PROGRAMA (hoisting de funciones)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * visitProgram implementa el hoisting de funciones en dos pasadas:
     *
     *   Pasada 1: Registrar todas las funciones de usuario y localizar main().
     *             Esto permite llamar a funciones antes de su definición textual.
     *
     *   Pasada 2: Generar código para main() primero, luego las demás funciones.
     *             El orden asegura que main sea el punto de entrada del ejecutable.
     */
    public function visitProgram($ctx)
    {
        $mainDecl = null;

        // ── Pasada 1: hoisting ────────────────────────────────────────────
        for ($i = 0; $i < $ctx->getChildCount() - 1; $i++) {
            $child = $ctx->getChild($i);
            if (!($child instanceof \Antlr\Antlr4\Runtime\ParserRuleContext)) continue;

            $funcDecl = $this->extractFuncDecl($child);
            if ($funcDecl === null) continue;

            $name = $funcDecl->ID()->getText();
            if ($name === 'main') {
                $mainDecl = $funcDecl;
            } else {
                $this->userFunctions[$name] = $funcDecl;
            }
        }

        if ($mainDecl === null) {
            $this->addError('Semántico', 'No se encontró la función main()', 0, 0);
            return null;
        }

        // ── Pasada 2: generación ──────────────────────────────────────────
        // main primero (punto de entrada del programa)
        $this->generateFunction($mainDecl);
        // Funciones de usuario después
        foreach ($this->userFunctions as $decl) {
            $this->generateFunction($decl);
        }

        return null;
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
            $this->addSymbol($p['name'], $p['type'], $name, 'param', $line, $col);
        }

        // ── Pasada 1: prescan del bloque (calcular FRAME_SIZE) ────────────
        if ($funcDecl->block()) {
            $this->prescanBlock($funcDecl->block());
        }

        $epilogueLabel             = '.epilogue_' . $name;
        $this->func->epilogueLabel = $epilogueLabel;
        $frameSize                 = $this->func->getFrameSize();

        // ── Prólogo ARM64 ─────────────────────────────────────────────────
        $this->textLines[] = '';
        $this->label($name);
        $this->comment("── función $name ── registro de activación ──");
        $this->emit('stp x29, x30, [sp, #-16]!', 'guardar fp (enlace control) y lr');
        $this->emit('mov x29, sp',                'establecer frame pointer');

        if ($frameSize > 0) {
            $this->emit("sub sp, sp, #$frameSize", "reservar $frameSize bytes (locales + params)");
        }

        // ── Copiar parámetros de registros al stack frame ─────────────────
        // Convención AArch64: int/bool/string/pointer en x0–x7, float32 en s0–s7
        $intReg   = 0;
        $floatReg = 0;
        foreach ($params as $p) {
            $offset = $this->func->getOffset($p['name']);
            if ($p['type'] === 'float32') {
                $reg = 's' . $floatReg++;
                $this->emit("str $reg, [x29, #-$offset]", "param {$p['name']} ($reg) → frame");
            } else {
                $reg = 'x' . $intReg++;
                $this->emit("str $reg, [x29, #-$offset]", "param {$p['name']} ($reg) → frame");
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
        if ($name === 'main') {
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