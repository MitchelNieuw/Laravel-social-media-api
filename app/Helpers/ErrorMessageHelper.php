<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use JsonException;

class ErrorMessageHelper
{
    /**
     * @param Exception $exception
     * @return JsonResponse
     */
    public function jsonErrorMessage(Exception $exception): JsonResponse {
        if ($exception->getCode() === 500) {
            Log::critical(json_encode($this->prepareInternalServerError($exception)));
        }
        $code = $exception->getCode();
        if ($exception->getCode() === 0) {
            $code = 500;
        }
        return response()->json($this->prepareErrorMessage($exception), $code);
    }

    /**
     * @param Exception $exception
     * @return array
     */
    public function prepareErrorMessage(Exception $exception): array
    {
        return [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];
    }

    /**
     * @param Exception $exception
     * @return array
     */
    public function prepareInternalServerError(Exception $exception): array
    {
        return [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];
    }
}
