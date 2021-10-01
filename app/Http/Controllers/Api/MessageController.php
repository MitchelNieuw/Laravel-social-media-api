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
    use ApiControllerTrait;

    public function __construct(
        public MessageService $messageService,
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function list(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $messages = (new MessageRepository())->getMessagesByUserId($user->getAttribute('id'));
            return MessageResource::collection($messages);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function store(Request $request): MessageResource|JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->messageService->storeMessage($request, $user);
            return new MessageResource($message);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function delete(int $messageId, Request $request): JsonResponse
    {
        try {
            $authenticatedUser = $this->checkUserOfTokenExists($request);
            $this->messageService->deleteMessage($messageId, $authenticatedUser->getAttribute('id'));
            return response()->json([
                'message' => 'Delete successful!',
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
