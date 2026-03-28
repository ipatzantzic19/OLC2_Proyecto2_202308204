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

// ── Traits de nivel superior (orquestadores) ──────────────────────────────────
require_once __DIR__ . '/Traits/EmitterTrait.php';
require_once __DIR__ . '/Traits/StringPoolTrait.php';
require_once __DIR__ . '/Traits/FloatOpsTrait.php';
require_once __DIR__ . '/Traits/StringOpsTrait.php';
require_once __DIR__ . '/Traits/LiteralsTrait.php';
require_once __DIR__ . '/Traits/AssignmentsTrait.php';
require_once __DIR__ . '/Traits/FunctionCallTrait.php';
require_once __DIR__ . '/Traits/HelpersTrait.php';

// ── Orquestadores con sub-traits (los require_once internos los cargan) ───────
require_once __DIR__ . '/Traits/ControlFlowTrait.php';
require_once __DIR__ . '/Traits/DeclarationsTrait.php';
require_once __DIR__ . '/Traits/ExpressionsTrait.php';

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
 * Traits de infraestructura (sin subcarpeta):
 *   EmitterTrait        → emit(), label(), comment(), buildAssembly()
 *   StringPoolTrait     → internString(), asmEscape()
 *   FloatOpsTrait       → internFloat(), fadd/fsub/fmul, conversiones int↔float
 *   StringOpsTrait      → golampi_concat, strlen, substr, now, typeOf
 *   LiteralsTrait       → visitIntLiteral, visitFloatLiteral, visitRune, etc.
 *   AssignmentsTrait    → visitSimpleAssignment, visitArrayAssignment, ++, --
 *   FunctionCallTrait   → fmt.Println, funciones usuario (multi-param), builtins
 *   HelpersTrait        → getTypeName, allocVar, storeDefault, addSymbol, visitIdentifier
 *
 * Traits orquestadores (con subcarpeta de sub-traits especializados):
 *
 *   ControlFlowTrait  ← orquesta:
 *     ControlFlow/ForClassicTrait    → for init ; cond ; post { }
 *     ControlFlow/ForWhileTrait      → for cond { }
 *     ControlFlow/ForInfiniteTrait   → for { }
 *     ControlFlow/IfTrait            → if / else if / else
 *     ControlFlow/SwitchTrait        → switch / case / default
 *     ControlFlow/TransferTrait      → break / continue / return
 *
 *   DeclarationsTrait ← orquesta:
 *     Declarations/PrescanTrait      → pasada 1: registrar variables antes de generar
 *     Declarations/VarDeclTrait      → var x T  /  var x T = expr
 *     Declarations/ShortVarDeclTrait → x := expr (tipo inferido)
 *     Declarations/ConstDeclTrait    → const x T = expr
 *
 *   ExpressionsTrait  ← orquesta:
 *     Expressions/ExpressionEntryTrait → visitExpression (punto de entrada)
 *     Expressions/LogicalOpsTrait      → ||, && con cortocircuito
 *     Expressions/ComparisonsTrait     → ==, !=, >, >=, <, <=
 *     Expressions/ArithmeticOpsTrait   → +, -, *, /, % con promoción de tipos
 *     Expressions/UnaryOpsTrait        → -, !, &, *, (expr), array access
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
    use EmitterTrait;
    use StringPoolTrait;
    use FloatOpsTrait;
    use StringOpsTrait;
    use LiteralsTrait;
    use AssignmentsTrait;
    use FunctionCallTrait;
    use HelpersTrait;

    // ── Traits orquestadores (cada uno importa sus sub-traits) ────────────
    use ControlFlowTrait;    // ForClassic + ForWhile + ForInfinite + If + Switch + Transfer
    use DeclarationsTrait;   // Prescan + VarDecl + ShortVarDecl + ConstDecl
    use ExpressionsTrait;    // Entry + Logical + Comparisons + Arithmetic + Unary

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