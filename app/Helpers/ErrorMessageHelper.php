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
     * @param \Exception $exception
     * @return RedirectResponse
     */
    public function redirectErrorMessage(\Exception $exception): RedirectResponse
    {
        Log::critical($exception->getMessage());
        return back()->withErrors(RedirectMessageEnum::OOPS_SOMETHING_WENT_WRONG);
    }
}