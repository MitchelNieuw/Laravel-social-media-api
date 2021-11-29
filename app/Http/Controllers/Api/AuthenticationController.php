<?php

namespace App\Http\Controllers\Api;

use Illuminate\Contracts\Container\BindingResolutionException;
use App\Exceptions\{PasswordException, UserException};
use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\AuthenticatedUserResource;
use App\Services\AuthenticationService;
use Exception;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Validation\ValidationException;

class AuthenticationController
{
    public function __construct(
        public ErrorMessageHelper $errorMessageHelper,
        public AuthenticationService $authenticationService
    )
    {
    }

    public function login(Request $request): JsonResponse|AuthenticatedUserResource
    {
        try {
            return new AuthenticatedUserResource($this->authenticationService->apiLogin($request));
        } catch (ValidationException | UserException | BindingResolutionException | PasswordException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function register(Request $request): JsonResponse|AuthenticatedUserResource
    {
        try {
            return new AuthenticatedUserResource($this->authenticationService->apiRegister($request));
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            auth('api')->logout();
            return response()->json([
                'message' => 'Logout successful!'
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
