<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\NotificationResource;
use Exception;
use Illuminate\Http\{JsonResponse, Request, Resources\Json\AnonymousResourceCollection};

class NotificationController
{
    use ApiControllerTrait;

    public function __construct(
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function list(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            return NotificationResource::collection($user->notifications()->get());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function delete(Request $request, string $id): JsonResponse
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $user->notifications()->where('id', '=', $id)->delete();
            return response()->json(['message' => 'Notification Deleted successfully!',]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
