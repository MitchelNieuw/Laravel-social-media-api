<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorMessageHelper;
use App\Services\ProfileService;
use Exception;
use Illuminate\Http\RedirectResponse;
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
     * @param ProfileService $profileService
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(ProfileService $profileService, ErrorMessageHelper $errorMessageHelper)
    {
        $this->profileService = $profileService;
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
     * @return \Illuminate\Contracts\View\Factory|RedirectResponse|\Illuminate\Routing\Redirector|View
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
     * TODO make pages for showing followers and following users
     * @param string $userTag
     */
    public function showFollowing(string $userTag)
    {

    }

    /**
     * @param string $userTag
     */
    public function showFollowers(string $userTag)
    {

    }
}
