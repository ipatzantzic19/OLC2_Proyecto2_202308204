<?php

namespace Golampi\Compiler\ARM64\Traits\Phases;

/**
 * PrescanPhase — Pasada 1: Escaneo previo (prescan) de variables y funciones
 *
 * Responsabilidad (Fase 0 - Análisis semántico):
 *   Recorrer el AST sin generar código para registrar:
 *   - Todas las variables locales con sus tipos
 *   - Arrays con dimensiones
 *   - Callee-saved registers necesarios
 *   - Calcular FRAME_SIZE del stack
 *
 * Concepto de compiladores (Aho et al.):
 *   Primera pasada (one-pass) o prescan de dos pasadas.
 *   Construye tabla de símbolos y descriptores antes de generar código.
 *   Permite validar tipos y calcular tamaño de frame sin generar aún.
 *
 * Ventajas:
 *   - Detecta variables no declaradas antes de generar código
 *   - Calcula frame size exacto para alineación AArch64
 *   - Valida tipos de expresiones
 *   - Permite optimizaciones en próximo pass
 */
trait PrescanPhase
{
    /**
     * Ejecuta prescan de un bloque: registra variables en FunctionContext (versión de fase).
     *
     * Recorre recursivamente el bloque visitando declaraciones y expresiones
     * para registrar todas las variables encontradas.
     *
     * Precondición: $this->func debe estar inicializado (nueva FunctionContext).
     * Postcondición: $this->func contiene descriptores de todas las variables.
     *
     * @param object $blockCtx contexto del bloque (block, statement*, etc.)
     * @param ?FunctionContext $context contexto opcional de función
     */
    protected function phasePrescanBlock($blockCtx, $context = null): void
    {
        if (!isset($blockCtx)) return;

        // Si se proporciona un nuevo contexto, reemplazar
        $oldFunc = $this->func;
        if ($context !== null) {
            $this->func = $context;
        }

        // Visitantes que extraen información sin generar código
        $children = $blockCtx->getChildCount();
        for ($i = 0; $i < $children; $i++) {
            $child = $blockCtx->getChild($i);

            // Saltar tokens de terminal (palabras clave, símbolos)
            if ($child instanceof \TerminalNode) {
                continue;
            }

            // Procesar nodos que declaran variables
            $class = class_basename($child);
            switch ($class) {
                case 'VarDeclContext':
                    $this->prescanVarDecl($child);
                    break;
                case 'ShortVarDeclContext':
                    $this->prescanShortVarDecl($child);
                    break;
                case 'ConstDeclContext':
                    $this->prescanConstDecl($child);
                    break;
                case 'StatementContext':
                case 'BlockContext':
                    // Recursivamente en bloques anidados (if, for, while, etc.)
                    $this->phasePrescanBlock($child);
                    break;
                case 'IfStmtContext':
                case 'ForStmtContext':
                case 'SwitchStmtContext':
                    // Control flow: escanear el bloque del bodyif existe
                    if (method_exists($child, 'block')) {
                        $this->phasePrescanBlock($child->block());
                    }
                    break;
            }
        }

        // Restaurar contexto anterior si fue cambiado
        if ($context !== null) {
            $this->func = $oldFunc;
        }
    }

    /**
     * Prescan de declaración de variable: var x T = expr
     * Extrae tipo y registra la variable en FunctionContext.
     */
    protected function prescanVarDecl($varDeclCtx): void
    {
        if (!isset($this->func)) return;

        // Patrón: var x T
        // varDeclCtx->identifierList()->getChild(0) = nombre
        // varDeclCtx->typeSpec() = tipo de la variable

        $identList = $varDeclCtx->identifierList();
        $typeCtx   = $varDeclCtx->typeSpec();

        if (!$identList || !$typeCtx) {
            return;
        }

        $type = $this->getTypeName($typeCtx);

        // Puede haber múltiples identificadores: var x, y, z T
        $count = $identList->getChildCount();
        for ($i = 0; $i < $count; $i++) {
            $child = $identList->getChild($i);
            if ($child instanceof \TerminalNode) {
                continue;
            }
            if (method_exists($child, 'getText')) {
                $name = $child->getText();
                // Registrar en FunctionContext
                $this->func->allocLocal($name, $type);
            }
        }
    }

    /**
     * Prescan de declaración corta: x := expr
     * Infiere tipo desde expresión y registra.
     */
    protected function prescanShortVarDecl($shortVarDeclCtx): void
    {
        if (!isset($this->func)) return;

        // Patrón: x := expr
        // Tipo se infiere desde la expresión

        $identList = $shortVarDeclCtx->identifierList();
        $exprList  = $shortVarDeclCtx->expressionList();

        if (!$identList || !$exprList) {
            return;
        }

        // Registrar cada identificador con tipo 'unknown' (será inferido después)
        $count = $identList->getChildCount();
        for ($i = 0; $i < $count; $i++) {
            $child = $identList->getChild($i);
            if ($child instanceof \TerminalNode) {
                continue;
            }
            if (method_exists($child, 'getText')) {
                $name = $child->getText();
                $this->func->allocLocal($name, 'unknown');
            }
        }
    }

    /**
     * Prescan de declaración const: const x T = expr
     * Similar a var pero inmutable.
     */
    protected function prescanConstDecl($constDeclCtx): void
    {
        if (!isset($this->func)) return;

        // Patrón: const name Type = expr
        $identList = $constDeclCtx->identifierList();
        $typeCtx   = $constDeclCtx->typeSpec();

        if (!$identList || !$typeCtx) {
            return;
        }

        $type = $this->getTypeName($typeCtx);

        $count = $identList->getChildCount();
        for ($i = 0; $i < $count; $i++) {
            $child = $identList->getChild($i);
            if ($child instanceof \TerminalNode) {
                continue;
            }
            if (method_exists($child, 'getText')) {
                $name = $child->getText();
                // Registrar como const (mismo storage que var, la diferencia es semántica)
                $this->func->allocLocal($name, $type);
            }
        }
    }

    /**
     * Prescan de declaración de array: var a [D1][D2]...T
     * Registra dimensiones en FunctionContext.
     */
    protected function prescanArrayDecl($arrayTypeCtx, string $name, string $elemType): void
    {
        if (!isset($this->func)) return;

        // Extraer dimensiones desde arrayTypeCtx
        // Patrón: [ expr ] [ expr ] ... T
        $dims = [];

        // Simplificado: si arrayTypeCtx tiene información de dimensión
        if (method_exists($arrayTypeCtx, 'getChildCount')) {
            $count = $arrayTypeCtx->getChildCount();
            for ($i = 0; $i < $count; $i++) {
                $child = $arrayTypeCtx->getChild($i);
                // Buscar números literales entre [ ]
                if (method_exists($child, 'getText')) {
                    $text = $child->getText();
                    if (is_numeric($text)) {
                        $dims[] = (int)$text;
                    }
                }
            }
        }

        if (empty($dims)) {
            $dims = [0];  // Array incompleto, error posterior
        }

        // Registrar en FunctionContext
        $this->func->allocArray($name, $dims, $elemType);
    }

    /**
     * Obtiene el nombre de tipo desde un contexto de tipo.
     *
     * @param object $typeCtx contexto sintáctico del tipo
     * @return string nombre del tipo: 'int32', 'float32', 'bool', 'string', 'rune'
     */
    protected function getTypeNameFromContext($typeCtx): string
    {
        if (!$typeCtx) return 'int32';

        $text = $typeCtx->getText();

        // Mapear texto a tipo interno
        $typeMap = [
            'int'     => 'int32',
            'int32'   => 'int32',
            'int64'   => 'int64',
            'float'   => 'float32',
            'float32' => 'float32',
            'float64' => 'float64',
            'bool'    => 'bool',
            'string'  => 'string',
            'rune'    => 'rune',
        ];

        return $typeMap[$text] ?? 'int32';
    }
}
