<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MessageException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use App\Services\MessageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @package App\Http\Controllers\Api
 */
class MessageController extends Controller
{
    use ApiControllerTrait;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @param MessageService $messageService
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(MessageService $messageService, ErrorMessageHelper $errorMessageHelper)
    {
        $this->messageService = $messageService;
        $this->errorMessageHelper = $errorMessageHelper;
    }

    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function list(Request $request)
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $messages = (new MessageRepository())->getMessagesByUserId($user->getAttribute('id'));
            return MessageResource::collection($messages);
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**v
     * @param Request $request
     * @return MessageResource|JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->messageService->storeMessage($request, $user);
            return new MessageResource($message);
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param int $messageId
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(int $messageId, Request $request): JsonResponse
    {
        try {
            $authenticatedUser = $this->checkUserOfTokenExists($request);
            $this->messageService->deleteMessage($messageId, $authenticatedUser->getAttribute('id'));
            return response()->json(['message' => 'Delete successful!',]);
        } catch (MessageException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}