<?php

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 */
class MessageException extends Exception
{
    /**
     * @var int
     */
    protected $code = 404;
}