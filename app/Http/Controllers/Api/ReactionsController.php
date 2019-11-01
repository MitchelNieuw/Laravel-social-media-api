<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MessageException;
use App\Exceptions\ReactionException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReactionResource;
use App\Repositories\UserRepository;
use App\Services\ReactionService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Api
 */
class ReactionsController extends Controller
{
    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @var ReactionService
     */
    protected $reactionService;

    /**
     * @param ErrorMessageHelper $errorMessageHelper
     * @param ReactionService $reactionService
     */
    public function __construct(ErrorMessageHelper $errorMessageHelper, ReactionService $reactionService)
    {
        $this->errorMessageHelper = $errorMessageHelper;
        $this->reactionService = $reactionService;
    }

    /**
     * @param Request $request
     * @param int $id
     * @return ReactionResource|JsonResponse
     */
    public function store(Request $request, int $id)
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $reaction = $this->reactionService->storeReaction($request, $user, $id);
            return new ReactionResource($reaction);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $reactionId
     * @return JsonResponse
     */
    public function delete(Request $request, int $id, int $reactionId): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->reactionService->deleteReaction($user, $id, $reactionId);
            return response()->json([
                'message' => $message,
            ]);
        } catch (ReactionException | MessageException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage(
                $exception,
                $exception->getCode(),
                $exception->getMessage()
            );
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @return User
     * @throws UserException
     */
    private function checkUserOfTokenExists(Request $request): User
    {
        $token = $request->bearerToken();
        $user = (new UserRepository())->getUserByJwtToken($token);
        if ($user === null) {
            throw new UserException('User with this token does not exist');
        }
        return $user;
    }
}