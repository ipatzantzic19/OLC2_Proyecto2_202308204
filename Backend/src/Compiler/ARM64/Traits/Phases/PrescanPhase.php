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
            $fullClass = get_class($child);
            $class = substr($fullClass, strrpos($fullClass, '\\') + 1);

            switch ($class) {
                case 'VarDeclSimpleContext':
                case 'VarDeclWithInitContext':
                case 'VarDeclContext':
                    $this->prescanVarDecl($child);
                    break;
                    
                case 'DeclarationContext':
                    // Buscar variable declarations dentro de Declaration
                    for ($j = 0; $j < $child->getChildCount(); $j++) {
                        $declChild = $child->getChild($j);
                        if ($declChild instanceof \TerminalNode) continue;
                        
                        $declFullClass = get_class($declChild);
                        $declClass = substr($declFullClass, strrpos($declFullClass, '\\') + 1);
                        
                        if ($declClass === 'VarDeclSimpleContext' || $declClass === 'VarDeclWithInitContext' || $declClass === 'VarDeclContext') {
                            $this->prescanVarDecl($declChild);
                        }
                    }
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
     * 
     * Fase 3: Detecta arrays y los registra con allocArray en lugar de allocLocal.
     */
    protected function prescanVarDecl($varDeclCtx): void
    {
        if (!isset($this->func)) return;

        // Obtener la lista de identificadores
        $idList = $varDeclCtx->idList();
        if (!$idList) return;

        // Obtener el tipo
        $typeCtx = $varDeclCtx->type();
        if (!$typeCtx) return;

        // Verificar si es un tipo array
        $arrayDims = $this->extractArrayDimensionsFromPhase($typeCtx);
        $type = $this->getTypeName($typeCtx);

        // Registrar cada identificador
        for ($i = 0; $i < $idList->getChildCount(); $i += 2) {
            $child = $idList->getChild($i);
            $name = $child->getText();

            if (!empty($arrayDims)) {
                // Es un array: registrar con allocArray
                $elemType = $this->extractArrayElementTypeFromPhase($typeCtx);
                $this->func->allocArray($name, $arrayDims, $elemType);
            } else {
                // Variable escalar: registrar con allocLocal
                $this->func->allocLocal($name, $type);
            }
        }
    }

    /**
     * Extrae dimensiones de un tipo array (versión para PrescanPhase).
     * Reutiliza la lógica de Prescan trait.
     */
    private function extractArrayDimensionsFromPhase($typeCtx): array
    {
        if ($typeCtx === null) return [];

        $dims = [];
        try {
            $current = $typeCtx;
            $visits = 0;
            $maxVisits = 100;

            while ($current !== null && $visits < $maxVisits) {
                $visits++;
                $class = get_class($current);
                $base  = substr($class, strrpos($class, '\\') + 1);

                if ($base === 'ArrayTypeContext') {
                    $expr = null;
                    if (is_callable([$current, 'expression'])) {
                        try {
                            $expr = $current->expression();
                        } catch (\Throwable $e) {}
                    }

                    if ($expr !== null) {
                        $dimValue = $this->evaluateLiteralExpressionFromPhase($expr);
                        
                        if ($dimValue !== null && $dimValue > 0) {
                            $dims[] = $dimValue;
                            
                            if (is_callable([$current, 'type'])) {
                                try {
                                    $current = $current->type();
                                } catch (\Throwable $e) {
                                    $current = null;
                                }
                            } else {
                                $current = null;
                            }
                        } else {
                            break;
                        }
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }
        } catch (\Throwable $e) {
            return [];
        }

        return $dims;
    }

    /**
     * Extrae el tipo de elemento de un array (versión para PrescanPhase).
     */
    private function extractArrayElementTypeFromPhase($typeCtx): string
    {
        if ($typeCtx === null) return 'int32';

        try {
            $current = $typeCtx;

            while ($current !== null) {
                $class = get_class($current);
                $base  = substr($class, strrpos($class, '\\') + 1);

                if ($base === 'ArrayTypeContext') {
                    if (is_callable([$current, 'type'])) {
                        try {
                            $current = $current->type();
                        } catch (\Throwable $e) {
                            break;
                        }
                    } else {
                        break;
                    }
                } else {
                    return $this->getTypeName($current);
                }
            }
        } catch (\Throwable $e) {}

        return 'int32';
    }

    /**
     * Evalúa una expresión literal para obtener su valor numérico (versión para PrescanPhase).
     */
    private function evaluateLiteralExpressionFromPhase($exprCtx): ?int
    {
        try {
            $class = get_class($exprCtx);
            $base  = substr($class, strrpos($class, '\\') + 1);

            // Caso 1: IntLiteralContext
            if ($base === 'IntLiteralContext') {
                $intToken = $exprCtx->INT32();
                if ($intToken !== null) {
                    return (int)$intToken->getText();
                }
            }

            // Caso 2: ExpressionContext con INT32 método
            if (is_callable([$exprCtx, 'INT32'])) {
                $intToken = $exprCtx->INT32();
                if ($intToken !== null) {
                    return (int)$intToken->getText();
                }
            }

            // Caso 3: ExpressionContext con intLiteral() o primaryExpr() método
            if (is_callable([$exprCtx, 'intLiteral'])) {
                try {
                    $intLit = $exprCtx->intLiteral();
                    if ($intLit !== null) {
                        return $this->evaluateLiteralExpressionFromPhase($intLit);  // Recurse
                    }
                } catch (\Throwable $e) {}
            }

            // Caso 4: Buscar en hijos directamente
            for ($i = 0; $i < $exprCtx->getChildCount(); $i++) {
                $child = $exprCtx->getChild($i);
                $childText = $child->getText();
                
                if ($child instanceof \TerminalNode) {
                    if (is_numeric($childText)) {
                        return (int)$childText;
                    }
                } else {
                    // Si es un contexto, intentar recursivo  
                    $rec = $this->evaluateLiteralExpressionFromPhase($child);
                    if ($rec !== null) {
                        return $rec;
                    }
                }
            }
        } catch (\Throwable $e) {}

        return null;
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
