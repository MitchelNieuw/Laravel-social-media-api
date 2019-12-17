<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @package App\Http\Controllers\Api
 */
class DashboardController extends Controller
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
    public function index(Request $request)
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $messages = (new MessageRepository())->getMessagesFromFollowingUsers($user->getAttribute('id'));
            return MessageResource::collection($messages);
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}