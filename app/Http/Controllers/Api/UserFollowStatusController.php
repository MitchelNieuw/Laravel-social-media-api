<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Services\{BanService, FollowService, NotificationService};
use Exception;
use Illuminate\Http\{JsonResponse, Request};

class UserFollowStatusController
{
    use ApiControllerTrait;

    public function __construct(
        public ErrorMessageHelper $errorMessageHelper,
        public FollowService $followService,
        public BanService $banService,
        public NotificationService $notificationService
    )
    {
    }

    public function follow(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->followService->follow($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function unFollow(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->followService->unFollow($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function ban(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->banService->banUserByTag($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function unBan(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->banService->unBanByUserTag($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function notificationsOn(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->notificationService->turnOnNotifications($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function notificationsOff(Request $request, string $tag): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->notificationService->turnOffNotifications($tag, $user->getAttribute('id'));
            return response()->json([
                'message' => $message,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
