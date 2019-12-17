<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BanException;
use App\Exceptions\FollowException;
use App\Exceptions\NotificationException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Services\BanService;
use App\Services\FollowService;
use App\Services\NotificationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Api
 */
class UserFollowStatusController extends Controller
{
    use ApiControllerTrait;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @var FollowService
     */
    protected $followService;

    /**
     * @var BanService
     */
    protected $banService;

    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * @param ErrorMessageHelper $errorMessageHelper
     * @param FollowService $followService
     * @param BanService $banService
     * @param NotificationService $notificationService
     */
    public function __construct(
        ErrorMessageHelper $errorMessageHelper,
        FollowService $followService,
        BanService $banService,
        NotificationService $notificationService
    ) {
        $this->errorMessageHelper = $errorMessageHelper;
        $this->followService = $followService;
        $this->banService = $banService;
        $this->notificationService = $notificationService;
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function follow(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->followService->follow($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (FollowException | UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function unFollow(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->followService->unFollow($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (FollowException | UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function ban(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->banService->banUserByTag($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (BanException | UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function unBan(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->banService->unBanByUserTag($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (BanException | UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function notificationsOn(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->notificationService->turnOnNotifications($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (NotificationException | UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function notificationsOff(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->notificationService->turnOffNotifications($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (NotificationException | UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}