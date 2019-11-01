<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
use App\Exceptions\FollowException;
use App\Exceptions\UserException;
use App\Follow;
use App\Repositories\FollowRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * @package App\Services
 */
class FollowService
{
    use ServiceTrait;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var FollowRepository
     */
    protected $followRepository;

    /**
     * @param UserRepository $userRepository
     * @param FollowRepository $followRepository
     */
    public function __construct(UserRepository $userRepository, FollowRepository $followRepository)
    {
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
    }

    /**
     * @param string $userTag
     * @param int $authenticatedUserId
     * @return string
     * @throws FollowException
     * @throws UserException
     */
    public function follow(string $userTag, int $authenticatedUserId): string
    {
        $userToFollow = $this->checkUserExists($userTag);
        $userToFollowId = $userToFollow->getAttribute('id');
        $this->checkUserTriesToFollowSelf($authenticatedUserId, $userToFollowId);
        $userFollowStatus = $this->getFollowStatusRecord($authenticatedUserId, $userToFollowId);
        if (!$this->createFollowStatusRecord($userFollowStatus, $authenticatedUserId, $userToFollowId, 1)) {
            $status = $this->getNewStatus($userFollowStatus, $authenticatedUserId, '|', 'follow');
            $this->followRepository->updateFollow($userFollowStatus, $status);
        }
        return ResponseMessageEnum::FOLLOW_SUCCESSFUL;
    }

    /**
     * @param string $userTag
     * @param int $authenticatedUserId
     * @return string
     * @throws FollowException
     * @throws UserException
     */
    public function unFollow(string $userTag, int $authenticatedUserId): string
    {
        $userToUnFollow = $this->checkUserExists($userTag);
        $userToUnFollowId = $userToUnFollow->getAttribute('id');
        $this->checkUserTriesToUnFollowSelf($authenticatedUserId, $userToUnFollowId);
        $this->updateFollowRecordStatus(
            $this->getFollowStatusUnFollow($authenticatedUserId, $userToUnFollowId, $userTag),
            $authenticatedUserId
        );
        return ResponseMessageEnum::UNFOLLOW_SUCCESSFUL;
    }

    /**
     * @param string $userTag
     * @return Collection
     * @throws UserException
     */
    public function getAllFollowing(string $userTag): Collection
    {
        $user = $this->checkUserExists($userTag);
        return $this->followRepository->getFollowingUsersWithRelationships($user->getAttribute('id'));
    }

    /**
     * @param string $userTag
     * @return Collection
     * @throws UserException
     */
    public function getAllFollowers(string $userTag): Collection
    {
        $user = $this->checkUserExists($userTag);
        $followers = $this->followRepository->getFollowersWithRelationships($user->getAttribute('id'));
        return $followers;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     */
    private function updateFollowRecordStatus(Follow $userFollowStatus, int $authenticatedUserId): void
    {
        $status = $this->getNewStatus($userFollowStatus, $authenticatedUserId, '^', 'follow');
        $this->followRepository->updateFollow($userFollowStatus, $status);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToFollowId
     * @return Follow
     * @throws FollowException
     */
    private function getFollowStatusRecord(int $authenticatedUserId, int $userToFollowId): Follow
    {
        $userFollowStatus = $this->followRepository->getFollowStatusForFollow($authenticatedUserId, $userToFollowId);
        if ($userFollowStatus === null) {
            throw new FollowException(ResponseMessageEnum::FOLLOWING_NOT_POSSIBLE);
        }
        return $userFollowStatus;
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToUnFollowId
     * @param string $userTag
     * @return Follow
     * @throws FollowException
     */
    private function getFollowStatusUnFollow(int $authenticatedUserId, int $userToUnFollowId, string $userTag): Follow
    {
        $userFollowStatus = $this->followRepository->getFollowStatusForUnFollow(
            $authenticatedUserId,
            $userToUnFollowId
        );
        if ($userFollowStatus === null) {
            throw new FollowException(ResponseMessageEnum::NOT_FOLLOWING_THIS_USER);
        }
        return $userFollowStatus;
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @throws FollowException
     */
    private function checkUserTriesToFollowSelf(int $authenticatedUserId, int $followUserId): void
    {
        if ($authenticatedUserId === $followUserId) {
            throw new FollowException(ResponseMessageEnum::FOLLOWING_SELF_NOT_POSSIBLE);
        }
    }

    /**
     * @param int $authenticatedUserId
     * @param int $unFollowUserId
     * @throws FollowException
     */
    private function checkUserTriesToUnFollowSelf(int $authenticatedUserId, int $unFollowUserId): void
    {
        if ($authenticatedUserId === $unFollowUserId) {
            throw new FollowException(ResponseMessageEnum::UNFOLLOWING_SELF_NOT_POSSIBLE);
        }
    }
}