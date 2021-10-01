<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use Exception;
use Illuminate\Http\{JsonResponse, Request, Resources\Json\AnonymousResourceCollection};

class DashboardController
{
    use ApiControllerTrait;

    public function __construct(
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $messages = (new MessageRepository())->getMessagesFromFollowingUsers($user->getAttribute('id'));
            return MessageResource::collection($messages);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
