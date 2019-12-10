<?php

namespace App\Helpers;

use App\Enums\ResponseMessageEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

/**
 * @package App\Helpers
 */
class ErrorMessageHelper
{
    /**
     * @param \Exception $exception
     * @return RedirectResponse
     */
    public function redirectErrorMessage(Exception $exception): RedirectResponse
    {
        Log::critical(json_encode($this->prepareErrorMessage($exception), JSON_THROW_ON_ERROR, 512));
        return back()->withErrors(ResponseMessageEnum::OOPS_SOMETHING_WENT_WRONG);
    }

    /**
     * @param Exception $exception
     * @return JsonResponse
     */
    public function jsonErrorMessage(
        Exception $exception
    ): JsonResponse {
        if ($exception->getCode() === 500) {
            Log::critical(json_encode($this->prepareInternalServerError($exception), JSON_THROW_ON_ERROR, 512));
        }
        return response()->json($this->prepareErrorMessage($exception), $exception->getCode());
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