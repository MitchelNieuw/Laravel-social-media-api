<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Services\{BanService, FollowService, NotificationService};
use Exception;
use Illuminate\Http\{JsonResponse, Request};

class UserFollowStatusController
{
    public function __construct(
        public ErrorMessageHelper $errorMessageHelper,
        public FollowService $followService,
        public BanService $banService,
        public NotificationService $notificationService
    )
    {
    }

    public function follow(string $tag): JsonResponse
    {
        try {
            return response()->json([
                'message' => $this->followService->follow($tag, auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function unFollow(string $tag): JsonResponse
    {
        try {
            return response()->json([
                'message' => $this->followService->unFollow($tag, auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function ban(string $tag): JsonResponse
    {
        try {
            return response()->json([
                'message' => $this->banService->banUserByTag($tag, auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function unBan(string $tag): JsonResponse
    {
        try {
            return response()->json([
                'message' => $this->banService->unBanByUserTag($tag, auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function notificationsOn(string $tag): JsonResponse
    {
        try {
            return response()->json([
                'message' => $this->notificationService->turnOnNotifications($tag, auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function notificationsOff(string $tag): JsonResponse
    {
        try {
            return response()->json([
                'message' => $this->notificationService->turnOffNotifications($tag, auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
