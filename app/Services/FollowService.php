<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
use App\Exceptions\{FollowException, UserException};
use App\Models\Follow;
use App\Repositories\{FollowRepository, UserRepository};
use Illuminate\Database\Eloquent\Collection;

class FollowService
{
    use ServiceTrait;

    public function __construct(
        protected UserRepository $userRepository,
        protected FollowRepository $followRepository
    )
    {
    }

    /**
     * @throws FollowException
     * @throws UserException
     */
    public function follow(string $userTag, int $authenticatedUserId): string
    {
        $userToFollowId = $this->checkUserExists($userTag)->id;
        $this->checkUserTriesToFollowSelf($authenticatedUserId, $userToFollowId);
        $userFollowStatus = $this->getFollowStatusRecord($authenticatedUserId, $userToFollowId);
        if (!$this->createFollowStatusRecord($userFollowStatus, $authenticatedUserId, $userToFollowId, 1)) {
            $this->followRepository->updateFollow(
                $userFollowStatus,
                $this->getNewStatus($userFollowStatus, $authenticatedUserId, '|', 'follow')
            );
        }
        return ResponseMessageEnum::FOLLOW_SUCCESSFUL;
    }

    /**
     * @throws FollowException
     * @throws UserException
     */
    public function unFollow(string $userTag, int $authenticatedUserId): string
    {
        $userToUnFollowId = $this->checkUserExists($userTag)->id;
        $this->checkUserTriesToUnFollowSelf($authenticatedUserId, $userToUnFollowId);
        $this->updateFollowRecordStatus(
            $this->getFollowStatusUnFollow($authenticatedUserId, $userToUnFollowId),
            $authenticatedUserId
        );
        return ResponseMessageEnum::UNFOLLOW_SUCCESSFUL;
    }

    /**
     * @throws UserException
     */
    public function getAllFollowers(string $userTag): Collection
    {
        return $this->followRepository->getFollowersWithRelationships($this->checkUserExists($userTag)->id);
    }

    private function updateFollowRecordStatus(Follow $userFollowStatus, int $authenticatedUserId): void
    {
        $this->followRepository->updateFollow(
            $userFollowStatus,
            $this->getNewStatus($userFollowStatus, $authenticatedUserId, '^', 'follow')
        );
    }

    /**
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
     * @throws FollowException
     */
    private function getFollowStatusUnFollow(int $authenticatedUserId, int $userToUnFollowId): Follow
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
     * @throws FollowException
     */
    private function checkUserTriesToFollowSelf(int $authenticatedUserId, int $followUserId): void
    {
        if ($authenticatedUserId === $followUserId) {
            throw new FollowException(ResponseMessageEnum::FOLLOWING_SELF_NOT_POSSIBLE);
        }
    }

    /**
     * @throws FollowException
     */
    private function checkUserTriesToUnFollowSelf(int $authenticatedUserId, int $unFollowUserId): void
    {
        if ($authenticatedUserId === $unFollowUserId) {
            throw new FollowException(ResponseMessageEnum::UNFOLLOWING_SELF_NOT_POSSIBLE);
        }
    }
}
