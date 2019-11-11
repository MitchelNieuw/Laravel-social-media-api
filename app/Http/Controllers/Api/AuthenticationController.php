<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PasswordException;
use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AuthenticationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @package App\Http\Controllers
 */
class AuthenticationController extends Controller
{
    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @param ErrorMessageHelper $errorMessageHelper
     * @param AuthenticationService $authenticationService
     */
    public function __construct(ErrorMessageHelper $errorMessageHelper, AuthenticationService $authenticationService)
    {
        $this->errorMessageHelper = $errorMessageHelper;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param Request $request
     * @return UserResource|JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $user = $this->authenticationService->apiLogin($request);
            return new UserResource($user);
        } catch (ValidationException | UserException | PasswordException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @return UserResource|JsonResponse
     */
    public function register(Request $request)
    {
        try {
            return new UserResource($this->authenticationService->apiRegister($request));
        } catch (BadRequestHttpException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}