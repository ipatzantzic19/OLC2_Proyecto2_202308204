<?php

namespace Golampi\Compiler\ARM64\Traits\Declarations;

use Golampi\Compiler\ARM64\Traits\Declarations\Prescan;
use Golampi\Compiler\ARM64\Traits\Declarations\VarDecl;
use Golampi\Compiler\ARM64\Traits\Declarations\ShortVarDecl;
use Golampi\Compiler\ARM64\Traits\Declarations\ConstDecl;

require_once __DIR__ . '/Declarations/Prescan.php';
require_once __DIR__ . '/Declarations/VarDecl.php';
require_once __DIR__ . '/Declarations/ShortVarDecl.php';
require_once __DIR__ . '/Declarations/ConstDecl.php';

/**
 * DeclHandler — Orquestador de la fase de declaraciones
 *
 * Coordina los cuatro sub-traits que implementan las declaraciones del
 * lenguaje Golampi según el modelo de compiladores en dos pasadas:
 *
 *   Pasada 1 — Prescan (PrescanTrait):
 *     Recorre el AST del bloque y registra todas las variables locales
 *     en el FunctionContext ANTES de generar código. Esto permite calcular
 *     el FRAME_SIZE correcto para el prólogo:  sub sp, sp, #FRAME_SIZE
 *
 *   Pasada 2 — Generación (VarDeclTrait | ShortVarDeclTrait | ConstDeclTrait):
 *     Genera el código de inicialización para cada declaración.
 *
 * Sub-traits y responsabilidades:
 *
 *   PrescanTrait      → Pasada 1: alocación de slots en el stack frame
 *                        prescanBlock() → prescanNode() → prescanIdList()
 *
 *   VarDeclTrait      → Pasada 2: var x T  y  var x T = expr
 *                        visitVarDeclSimple()   → valor por defecto
 *                        visitVarDeclWithInit() → inicialización explícita
 *
 *   ShortVarDeclTrait → Pasada 2: x := expr  (tipo inferido de la expresión)
 *                        visitShortVarDecl()    → tipo inferido + almacenar
 *
 *   ConstDeclTrait    → Pasada 2: const x T = expr  (inmutable en semántica)
 *                        visitConstDecl()        → igual que var pero con etiqueta (const)
 *
 * Estado compartido (definido en ARM64Generator):
 *   ?FunctionContext $func → función actual con su tabla de slots
 *   array  $symbolTable   → tabla de símbolos del compilador
 *   array  $errors        → errores semánticos acumulados
 *
 * Passthrough de declaration y statement:
 *   Estos nodos son "contenedores" del AST que solo despachan al hijo
 *   correcto. Los mantenemos aquí por cohesión semántica.
 */
trait DeclHandler
{
    // ── Pasada 1: pre-escáner de variables ────────────────────────────────
    use Prescan;      // prescanBlock, prescanNode, prescanIdList

    // ── Pasada 2: generación de código ───────────────────────────────────
    use VarDecl;      // var x T  /  var x T = expr
    use ShortVarDecl; // x := expr  (tipo inferido)
    use ConstDecl;    // const x T = expr

    // ── Passthrough de nodos contenedor ──────────────────────────────────

    /**
     * visitDeclaration: el nodo declaration del AST delega al hijo
     * correcto (varDecl, constDecl, funcDecl o statement).
     */
    public function visitDeclaration($ctx)
    {
        return $this->visitChildren($ctx);
    }

    /**
     * visitStatement: el nodo statement delega al hijo correcto.
     */
    public function visitStatement($ctx)
    {
        return $this->visitChildren($ctx);
    }

    /**
     * Las declaraciones de función se manejan en ARM64Generator::visitProgram()
     * mediante el mecanismo de hoisting. Aquí son no-op para evitar
     * que el visitChildren las procese dos veces.
     */
    public function visitFuncDeclSingleReturn($ctx) { return null; }
    public function visitFuncDeclMultiReturn($ctx)  { return null; }
}