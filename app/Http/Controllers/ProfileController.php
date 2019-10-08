<?php

namespace App\Http\Controllers;

use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Services\FollowService;
use App\Services\ProfileService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

/**
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * @var ProfileService
     */
    protected $profileService;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @var FollowService
     */
    protected $followService;

    /**
     * @param ProfileService $profileService
     * @param ErrorMessageHelper $errorMessageHelper
     * @param FollowService $followService
     */
    public function __construct(
        ProfileService $profileService,
        ErrorMessageHelper $errorMessageHelper,
        FollowService $followService
    ) {
        $this->profileService = $profileService;
        $this->followService = $followService;
        $this->errorMessageHelper = $errorMessageHelper;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return $this->profileService->displayProfile();
    }

    /**
     * @param string $userTag
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show(string $userTag)
    {
        try {
            return $this->profileService->displayUser($userTag);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }

    /**
     * @param string $userTag
     * @return Factory|RedirectResponse|View
     */
    public function showFollowing(string $userTag)
    {
        try {
            $following = $this->followService->getAllFollowing($userTag);
            return view('following', compact('following', 'userTag'));
        } catch (UserException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }

    /**
     * @param string $userTag
     * @return Factory|RedirectResponse|View
     */
    public function showFollowers(string $userTag)
    {
        try {
            $followers = $this->followService->getAllFollowers($userTag);
            return view('followers', compact('followers', 'userTag'));
        } catch (UserException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }
}
