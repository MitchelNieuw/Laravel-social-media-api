<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\NotificationResource;
use Exception;
use Illuminate\Http\{JsonResponse, Resources\Json\AnonymousResourceCollection};

class NotificationController
{
    public function __construct(
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function list(): JsonResponse|AnonymousResourceCollection
    {
        try {
            return NotificationResource::collection(
                auth('api')->user()->notifications()->get()
            );
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            auth('api')->user()->notifications()->find($id)->delete();
            return response()->json([
                'message' => 'Notification Deleted successfully!',
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
