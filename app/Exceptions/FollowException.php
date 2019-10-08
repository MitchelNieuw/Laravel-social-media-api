<?php

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 */
class FollowException extends Exception
{
    /**
     * @var int
     */
    protected $code = 404;
}