<?php

namespace App\Http\Controllers;

use App\Exceptions\BanException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Services\BanService;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers
 */
class BanController extends Controller
{
    /**
     * @var BanService
     */
    protected $banService;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @param BanService $banService
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(BanService $banService, ErrorMessageHelper $errorMessageHelper)
    {
        $this->banService = $banService;
        $this->errorMessageHelper = $errorMessageHelper;
    }

    /**
     * @param string $userTag
     * @return RedirectResponse
     */
    public function ban(string $userTag): RedirectResponse
    {
        try {
            $message = $this->banService->banUserByTag($userTag, auth()->user()->getAuthIdentifier());
            return back()->with(['message' => $message]);
        } catch (BanException | UserException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }

    /**
     * @param string $userTag
     * @return RedirectResponse
     */
    public function unBan(string $userTag): RedirectResponse
    {
        try {
            $message = $this->banService->unBanByUserTag($userTag, auth()->user()->getAuthIdentifier());
            return back()->with(['message' => $message]);
        } catch (BanException | UserException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }
}
