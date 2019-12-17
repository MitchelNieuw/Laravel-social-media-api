<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Repositories\UserRepository;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @package App\Http\Controllers\Api
 */
class NotificationController extends Controller
{
    use ApiControllerTrait;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(ErrorMessageHelper $errorMessageHelper)
    {
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
            return NotificationResource::collection($user->notifications()->get());
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
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