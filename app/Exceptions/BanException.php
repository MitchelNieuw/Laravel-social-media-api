<?php

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 */
class BanException extends Exception
{
    /**
     * @var int
     */
    protected $code = 404;
}