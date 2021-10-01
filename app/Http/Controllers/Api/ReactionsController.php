<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\ReactionResource;
use App\Services\ReactionService;
use Exception;
use Illuminate\Http\{JsonResponse, Request};

class ReactionsController
{
    use ApiControllerTrait;

    public function __construct(
        public ErrorMessageHelper $errorMessageHelper,
        public ReactionService $reactionService
    ) {}

    public function store(Request $request, int $id): JsonResponse|ReactionResource
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $reaction = $this->reactionService->storeReaction($request, $user, $id);
            return new ReactionResource($reaction);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function delete(Request $request, int $id, int $reactionId): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $message = $this->reactionService->deleteReaction($user, $id, $reactionId);
            return response()->json([
                'message' => $message,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
