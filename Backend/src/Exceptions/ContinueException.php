<?php

namespace Golampi\Exceptions;

use Exception;

/**
 * Excepción para manejar la sentencia continue
 */
class ContinueException extends Exception
{
    public function __construct()
    {
        parent::__construct("continue");
    }
}
