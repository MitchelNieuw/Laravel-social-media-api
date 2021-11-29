<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class BanException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;
}
