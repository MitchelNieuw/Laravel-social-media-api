<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\DisplayUserResource;
use App\Repositories\{FollowRepository, UserRepository};
use Exception;
use Illuminate\Http\JsonResponse;

class ProfileController
{
    public function __construct(
        public FollowRepository $followRepository,
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function show(string $tag): JsonResponse|DisplayUserResource
    {
        try {
            if (($user = (new UserRepository())->getUserByTagWithMessages($tag)) === null) {
                throw new UserException("User with the tag $tag doesnt exist");
            }
            return new DisplayUserResource($user, auth('api')->id());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function getFollowers(): JsonResponse
    {
        try {
            return response()->json([
                'followers' => $this->followRepository->getFollowersWithRelationships(auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function getFollowing(): JsonResponse
    {
        try {
            return response()->json([
                'following' => $this->followRepository->getFollowingUsersWithRelationships(auth('api')->id()),
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
