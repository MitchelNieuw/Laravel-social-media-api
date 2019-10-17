<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MessageException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Services\MessageService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @package App\Http\Controllers\Api
 */
class MessageController extends Controller
{
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
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $token = request()->bearerToken();
            $user = (new UserRepository())->getUserByJwtToken($token);
            if ($user === null) {
                throw new UserException('User with this token does not exist');
            }
            $messages = (new MessageRepository())->getAllMessagesByUserId($user->getAttribute('id'));
            return response()->json($messages);
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists();
            $message = $this->messageService->storeMessage(
                $request,
                $user->getAttribute('id'),
                $request->get('content')
            );
            return response()->json($message);
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception, $exception->getMessage());
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
            $authenticatedUser = JWTAuth::toUser($request->headers->get('Authorization'));
            $this->messageService->deleteMessage($messageId, $authenticatedUser->id);
            return response()->json([
                'message' => 'Delete successful!',
            ]);
        } catch (MessageException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @return User
     * @throws UserException
     */
    private function checkUserOfTokenExists(): User
    {
        $token = request()->bearerToken();
        $user = (new UserRepository())->getUserByJwtToken($token);
        if ($user === null) {
            throw new UserException('User with this token does not exist');
        }
        return $user;
    }
}