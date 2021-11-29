<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class MessageException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;
}
