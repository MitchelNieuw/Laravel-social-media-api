<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\DisplayUserResource;
use App\Models\User;
use App\Repositories\{FollowRepository, UserRepository};
use Exception;
use Illuminate\Http\{JsonResponse, Request};

class ProfileController
{
    use ApiControllerTrait;

    public function __construct(
        public FollowRepository   $followRepository,
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function show(Request $request, string $tag): JsonResponse|DisplayUserResource
    {
        try {
            $authenticatedUser = $this->checkUserOfTokenExists($request);
            $user = (new UserRepository())->getUserByTagWithMessages($tag);
            if ($user === null) {
                throw new UserException('User with the tag ' . $tag . ' doesnt exist');
            }
            return new DisplayUserResource($user, $authenticatedUser->getAttribute('id'));
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    public function getFollowers(Request $request, string $tag): JsonResponse
    {
        try {
            $this->checkUserOfTokenExists($request);
            $user = $this->checkUserWithTagExist($tag);
            $followers = $this->followRepository->getFollowersWithRelationships($user->getAttribute('id'));
            return response()->json([
                'followers' => $followers,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param string $tag
     * @return User
     * @throws UserException
     */
    private function checkUserWithTagExist(string $tag): User
    {
        if (($user = (new UserRepository())->getUserByUserTag($tag)) === null) {
            throw new UserException('user with this tag doesnt exist');
        }
        return $user;
    }

    public function getFollowing(Request $request, string $tag): JsonResponse
    {
        try {
            $this->checkUserOfTokenExists($request);
            $user = $this->checkUserWithTagExist($tag);
            $following = $this->followRepository->getFollowingUsersWithRelationships($user->getAttribute('id'));
            return response()->json([
                'following' => $following,
            ]);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
