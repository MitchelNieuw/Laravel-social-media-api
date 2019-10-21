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
        Log::critical($exception->getMessage());
        return back()->withErrors(ResponseMessageEnum::OOPS_SOMETHING_WENT_WRONG);
    }

    /**
     * @param Exception $exception
     * @param string $message
     * @return JsonResponse
     */
    public function jsonErrorMessage(
        Exception $exception,
        string $message = ResponseMessageEnum::OOPS_SOMETHING_WENT_WRONG
    ): JsonResponse {
        Log::critical($exception->getMessage());
        return response()->json([
            'message' => $message,
        ], 404);
    }
}