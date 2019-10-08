<?php

namespace App\Http\Controllers;

use App\Exceptions\FollowException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Repositories\FollowRepository;
use App\Repositories\UserRepository;
use App\Services\FollowService;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers
 */
class FollowController extends Controller
{
    /**
     * @var FollowRepository
     */
    protected $followRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var FollowService
     */
    protected $followService;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @param FollowRepository $followRepository
     * @param UserRepository $userRepository
     * @param FollowService $followService
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(
        FollowRepository $followRepository,
        UserRepository $userRepository,
        FollowService $followService,
        ErrorMessageHelper $errorMessageHelper
    ) {
        $this->followRepository = $followRepository;
        $this->userRepository = $userRepository;
        $this->followService = $followService;
        $this->errorMessageHelper = $errorMessageHelper;
    }

    /**
     * @param string $userTag
     * @return RedirectResponse
     */
    public function follow(string $userTag): RedirectResponse
    {
        try {
            $message = $this->followService->follow($userTag);
            return back()->with(['message' => $message]);
        } catch (FollowException | UserException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }

    /**
     * @param string $userTag
     * @return RedirectResponse
     */
    public function unFollow(string $userTag): RedirectResponse
    {
        try {
            $message = $this->followService->unFollow($userTag);
            return back()->with(['message' => $message]);
        } catch (FollowException | UserException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }
}
