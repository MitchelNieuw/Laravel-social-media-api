<?php

namespace App\Http\Controllers;

use App\Exceptions\NotificationException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Services\NotificationService;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers
 */
class NotificationController extends Controller
{
    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(ErrorMessageHelper $errorMessageHelper, NotificationService $notificationService)
    {
        $this->errorMessageHelper = $errorMessageHelper;
        $this->notificationService = $notificationService;
    }

    /**
     * @param string $userTag
     * @return RedirectResponse|null
     */
    public function turnOnNotifications(string $userTag): ?RedirectResponse
    {
        try {
            $this->notificationService->turnOnNotifications($userTag);
            return back();
        } catch (UserException | NotificationException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }

    /**
     * @param string $userTag
     * @return RedirectResponse|null
     */
    public function turnOffNotifications(string $userTag): ?RedirectResponse
    {
        try {
            $this->notificationService->turnOffNotifications($userTag);
            return back();
        } catch (UserException | NotificationException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }

    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function delete(string $id): RedirectResponse
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back();
    }

    /**
     * @return RedirectResponse
     */
    public function deleteAll(): RedirectResponse
    {
        auth()->user()->notifications()->delete();
        return back();
    }
}