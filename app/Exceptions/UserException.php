<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UserException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;
}
