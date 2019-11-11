<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Helpers\ErrorMessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\DisplayUserResource;
use App\Repositories\FollowRepository;
use App\Repositories\UserRepository;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Api
 */
class ProfileController extends Controller
{
    /**
     * @var FollowRepository
     */
    protected $followRepository;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @param FollowRepository $followRepository
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(FollowRepository $followRepository, ErrorMessageHelper $errorMessageHelper)
    {
        $this->followRepository = $followRepository;
        $this->errorMessageHelper = $errorMessageHelper;
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return DisplayUserResource|JsonResponse
     */
    public function show(Request $request, string $tag)
    {
        try {
            $authenticatedUser = $this->checkUserOfTokenExists($request);
            $user = (new UserRepository())->getUserByTagWithMessages($tag);
            if ($user === null) {
                throw new UserException('User with the tag ' . $tag . ' doesnt exist');
            }
            return new DisplayUserResource($user, $authenticatedUser->getAttribute('id'));
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function getFollowers(Request $request, string $tag): JsonResponse
    {
        try {
            $this->checkUserOfTokenExists($request);
            $user = (new UserRepository())->getUserByUserTag($tag);
            if ($user === null) {
                throw new UserException('user with this tag doesnt exist');
            }
            $followers = $this->followRepository->getFollowersWithRelationships($user->getAttribute('id'));
            return response()->json(['followers' => $followers,]);
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        } catch (Exception $exception) {
            return  $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return JsonResponse
     */
    public function getFollowing(Request $request, string $tag): JsonResponse
    {
        try {
            $this->checkUserOfTokenExists($request);
            $user = (new UserRepository())->getUserByUserTag($tag);
            if ($user === null) {
                throw new UserException('user with this tag doesnt exist');
            }
            $following = $this->followRepository->getFollowingUsersWithRelationships($user->getAttribute('id'));
            return response()->json(['following' => $following,]);
        } catch (UserException $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
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