<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use App\Services\MessageService;
use Exception;
use Illuminate\Http\{JsonResponse, Request, Resources\Json\AnonymousResourceCollection};

class MessageController
{
    public function __construct(
        public MessageService $messageService,
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function list(): JsonResponse|AnonymousResourceCollection
    {
        try {
            return MessageResource::collection(
                (new MessageRepository())->getMessagesByUserId(auth('api')->id())
            );
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function store(Request $request): MessageResource|JsonResponse
    {
        try {
            return new MessageResource(
                $this->messageService->storeMessage($request, auth('api')->user())
            );
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function delete(int $messageId): JsonResponse
    {
        try {
            $this->messageService->deleteMessage($messageId, auth('api')->id());
            return response()->json([
                'message' => 'Delete successful!',
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
