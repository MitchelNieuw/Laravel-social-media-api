<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use Exception;
use Illuminate\Http\{JsonResponse, Resources\Json\AnonymousResourceCollection};

class DashboardController
{
    public function __construct(
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function index(): JsonResponse|AnonymousResourceCollection
    {
        try {
            return MessageResource::collection(
                (new MessageRepository)->getMessagesFromFollowingUsers(auth('api')->id())
            );
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
