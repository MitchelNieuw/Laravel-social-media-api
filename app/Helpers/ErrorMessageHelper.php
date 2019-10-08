<?php

namespace App\Helpers;

use App\Enums\RedirectMessageEnum;
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
        Log::critical(json_encode($exception));
        return back()->withErrors(RedirectMessageEnum::OOPS_SOMETHING_WENT_WRONG);
    }
}