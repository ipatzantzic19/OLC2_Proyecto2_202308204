<?php

namespace Golampi\Exceptions;

use Exception;

/**
 * Excepción para manejar la sentencia break
 */
class BreakException extends Exception
{
    public function __construct()
    {
        parent::__construct("break");
    }
}
