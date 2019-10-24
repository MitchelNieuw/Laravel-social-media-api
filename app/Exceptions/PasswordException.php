<?php

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 */
class PasswordException extends Exception
{
    /**
     * @var int
     */
    protected $code = 400;
}