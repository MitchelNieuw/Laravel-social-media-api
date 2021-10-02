<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\ReactionResource;
use App\Services\ReactionService;
use Exception;
use Illuminate\Http\{JsonResponse, Request};

class ReactionsController
{
    public function __construct(
        public ErrorMessageHelper $errorMessageHelper,
        public ReactionService $reactionService
    ) {}

    public function store(Request $request, int $id): JsonResponse|ReactionResource
    {
        try {
            return new ReactionResource(
                $this->reactionService->storeReaction($request, auth('api')->user(), $id)
            );
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function delete(int $id, int $reactionId): JsonResponse
    {
        try {
            return response()->json([
                'message' => $this->reactionService->deleteReaction(auth('api')->user(), $id, $reactionId),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
