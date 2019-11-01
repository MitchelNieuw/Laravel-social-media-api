<?php

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 */
class ReactionException extends Exception
{
    /**
     * @var int
     */
    protected $code = 400;
}