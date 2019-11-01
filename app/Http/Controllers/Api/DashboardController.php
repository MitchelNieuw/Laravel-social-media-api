<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\User;
use Exception;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Api
 */
class DashboardController extends Controller
{
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $user = $this->checkUserOfTokenExists($request);
            $messages = (new MessageRepository())->getMessagesFromFollowingUsers($user->getAttribute('id'));
            return MessageResource::collection($messages);
        } catch (UserException $exception) {
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