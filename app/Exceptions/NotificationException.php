<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NotificationException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;
}
