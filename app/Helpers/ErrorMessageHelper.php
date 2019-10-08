<?php

namespace App\Helpers;

use App\Enums\RedirectErrorMessageEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

/**
 * @package App\Helpers
 */
class ErrorMessageHelper
{
    /**
     * @param $exception
     * @return RedirectResponse
     */
    public function redirectErrorMessage($exception): RedirectResponse
    {
        Log::critical(json_encode($exception, JSON_THROW_ON_ERROR));
        return back()->withErrors(RedirectErrorMessageEnum::OOPS_SOMETHING_WENT_WRONG);
    }
}