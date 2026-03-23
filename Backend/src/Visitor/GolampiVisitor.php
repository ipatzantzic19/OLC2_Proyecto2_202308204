<?php

namespace Golampi\Visitor;

use Golampi\Runtime\Value;
use Golampi\Runtime\Environment;
use Golampi\Traits\ArithmeticOperations;
use Golampi\Traits\RelationalOperations;
use Golampi\Traits\ExpressionVisitor;
use Golampi\Traits\DeclarationVisitor;
use Golampi\Traits\StatementVisitor;
use Golampi\Traits\AssignmentVisitor;
use Golampi\Traits\ControlFlowVisitor;
use Golampi\Traits\IncrementDecrementVisitor;
use Golampi\Traits\FunctionVisitor;
use Golampi\Traits\ArrayVisitor;
use Golampi\Traits\BuiltinFunctionsVisitor;

/**
 * Visitor principal del intérprete de Golampi
 */
class GolampiVisitor extends BaseVisitor
{
    use ArithmeticOperations;
    use RelationalOperations;
    use ExpressionVisitor;
    use DeclarationVisitor;
    use StatementVisitor;
    use AssignmentVisitor;
    use ControlFlowVisitor;
    use IncrementDecrementVisitor;
    use FunctionVisitor;
    use ArrayVisitor;   
    use BuiltinFunctionsVisitor;

    public function __construct()
    {
        parent::__construct();

        // Registrar el espacio de nombres `fmt` como una variable especial
        $this->environment->define('fmt', Value::string('namespace'));
    }
}