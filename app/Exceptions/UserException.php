<?php

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 */
class UserException extends Exception
{
    /**
     * @var int
     */
    protected $code = 404;
}