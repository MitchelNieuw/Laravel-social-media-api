<?php

namespace App\Services;

use App\Enums\RedirectMessageEnum;
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
     * @return string
     * @throws FollowException
     * @throws UserException
     */
    public function follow(string $userTag): string
    {
        $authenticatedUserId = auth()->user()->getAuthIdentifier();
        $userToFollow = $this->checkUserExists($userTag);
        $userToFollowId = $userToFollow->getAttribute('id');
        $this->checkIfUserTriesToFollowSelf($authenticatedUserId, $userToFollowId);
        $userFollowStatus = $this->getFollowStatusRecord($authenticatedUserId, $userToFollowId, $userTag);
        if (!$this->createFollowStatusRecord($userFollowStatus, $authenticatedUserId, $userToFollowId, 1)) {
            $status = $this->getNewFollowStatus($userFollowStatus, $authenticatedUserId);
            $this->followRepository->updateFollow($userFollowStatus, $status);
        }
        return RedirectMessageEnum::FOLLOW_SUCCESSFUL;
    }

    /**
     * @param string $userTag
     * @return string
     * @throws FollowException
     * @throws UserException
     */
    public function unFollow(string $userTag): string
    {
        $authenticatedUserId = auth()->user()->getAuthIdentifier();
        $userToUnFollow = $this->checkUserExists($userTag);
        $userToUnFollowId = $userToUnFollow->getAttribute('id');
        $this->checkIfUserTriesToUnFollowSelf($authenticatedUserId, $userToUnFollowId);
        $this->updateFollowRecordStatus(
            $this->getFollowStatusUnFollow($authenticatedUserId, $userToUnFollowId, $userTag),
            $authenticatedUserId
        );
        return RedirectMessageEnum::UNFOLLOW_SUCCESSFUL;
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
        $followers = $this->followRepository->getFollowersWithRelationsByUserId($user->getAttribute('id'));
        return $followers;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     */
    private function updateFollowRecordStatus(Follow $userFollowStatus, int $authenticatedUserId): void
    {
        $status = $this->getNewUnFollowStatus($userFollowStatus, $authenticatedUserId);
        $this->followRepository->updateFollow($userFollowStatus, $status);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToFollowId
     * @param string $userTag
     * @return Follow
     * @throws FollowException
     */
    private function getFollowStatusRecord(int $authenticatedUserId, int $userToFollowId, string $userTag): Follow
    {
        $userFollowStatus = $this->followRepository->getFollowStatusForFollow($authenticatedUserId, $userToFollowId);
        if ($userFollowStatus === null) {
            throw new FollowException(RedirectMessageEnum::FOLLOWING_NOT_POSSIBLE);
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
            throw new FollowException(RedirectMessageEnum::NOT_FOLLOWING_THIS_USER);
        }
        return $userFollowStatus;
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @throws FollowException
     */
    private function checkIfUserTriesToFollowSelf(int $authenticatedUserId, int $followUserId): void
    {
        if ($authenticatedUserId === $followUserId) {
            throw new FollowException(RedirectMessageEnum::FOLLOWING_SELF_NOT_POSSIBLE);
        }
    }

    /**
     * @param int $authenticatedUserId
     * @param int $unFollowUserId
     * @throws FollowException
     */
    private function checkIfUserTriesToUnFollowSelf(int $authenticatedUserId, int $unFollowUserId): void
    {
        if ($authenticatedUserId === $unFollowUserId) {
            throw new FollowException(RedirectMessageEnum::UNFOLLOWING_SELF_NOT_POSSIBLE);
        }
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $userId
     * @return int
     */
    private function getNewFollowStatus(Follow $userFollowStatus, int $userId): int
    {
        $status = $userFollowStatus->getAttribute('status');
        if ($userFollowStatus->getAttribute('user_id') === $userId) {
            $status = $userFollowStatus->getAttribute('status') | 1 << 0;
        }
        if ($userFollowStatus->getAttribute('follow_user_id') === $userId) {
            $status = $userFollowStatus->getAttribute('status') | 1 << 1;
        }
        return $status;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $userId
     * @return int
     */
    private function getNewUnFollowStatus(Follow $userFollowStatus, int $userId): int
    {
        $status = $userFollowStatus->getAttribute('status');
        if ($userFollowStatus->getAttribute('user_id') === $userId) {
            $status = $userFollowStatus->getAttribute('status') ^ 1 << 0;
        }
        if ($userFollowStatus->getAttribute('follow_user_id') === $userId) {
            $status = $userFollowStatus->getAttribute('status') ^ 1 << 1;
        }
        return $status;
    }
}