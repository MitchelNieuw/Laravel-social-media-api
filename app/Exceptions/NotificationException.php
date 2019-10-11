<?php

namespace App\Exceptions;

use Exception;

/**
 * @package App\Exceptions
 */
class NotificationException extends Exception
{
    /**
     * @var int
     */
    protected $code = 404;
}