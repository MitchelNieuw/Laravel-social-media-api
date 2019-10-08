<?php

namespace App\Services;

use App\Enums\RedirectMessageEnum;
use App\Exceptions\BanException;
use App\Exceptions\UserException;
use App\Follow;
use App\Repositories\FollowRepository;
use App\Repositories\UserRepository;

/**
 * @package App\Services
 */
class BanService
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
     * @throws BanException
     * @throws UserException
     */
    public function banUserByTag(string $userTag): string
    {
        $authenticatedUserId = auth()->user()->getAuthIdentifier();
        $userToBan = $this->checkUserExists($userTag);
        $userToBanId = $userToBan->getAttribute('id');
        $this->checkUserBanSelf($authenticatedUserId, $userToBanId);
        $userFollowStatus = $this->checkUserGotUnBanned($authenticatedUserId, $userToBanId);
        if (!$this->createFollowStatusRecord($userFollowStatus, $authenticatedUserId, $userToBanId, 4)){
            $this->updateFollowRecordBanUser($userFollowStatus, $authenticatedUserId);
        }
        return RedirectMessageEnum::BAN_SUCCESSFUL;
    }

    /**
     * @param string $userTag
     * @return string
     * @throws BanException
     * @throws UserException
     */
    public function unBanByUserTag(string $userTag): string
    {
        $authenticatedUserId = auth()->user()->getAuthIdentifier();
        $userToUnBan = $this->checkUserExists($userTag);
        $userToUnBanId = $userToUnBan->getAttribute('id');
        $this->checkUserUnBanSelf($authenticatedUserId, $userToUnBanId);
        $userFollowStatus = $this->checkUserGotBanned($userTag, $authenticatedUserId, $userToUnBanId);
        $this->updateFollowRecordUnBanUser($userFollowStatus, $authenticatedUserId);
        return RedirectMessageEnum::UNBAN_SUCCESSFUL;
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToBanId
     * @return Follow
     * @throws BanException
     */
    private function checkUserGotUnBanned(int $authenticatedUserId, int $userToBanId): Follow
    {
        $userFollowStatus = $this->followRepository->getFollowStatusForBan($authenticatedUserId, $userToBanId);
        if ($userFollowStatus === null) {
            throw new BanException(RedirectMessageEnum::USER_ALREADY_UNBANNED);
        }
        return $userFollowStatus;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     */
    private function updateFollowRecordBanUser(Follow $userFollowStatus, int $authenticatedUserId): void
    {
        if ($userFollowStatus !== null) {
            $status = $this->getNewBanStatus($userFollowStatus, $authenticatedUserId);
            $this->followRepository->updateFollow($userFollowStatus, $status);
        }
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     */
    private function updateFollowRecordUnBanUser(Follow $userFollowStatus, int $authenticatedUserId): void
    {
        if ($userFollowStatus !== null) {
            $status = $this->getNewUnBanStatus($userFollowStatus, $authenticatedUserId);
            $this->followRepository->updateFollow($userFollowStatus, $status);
        }
    }

    /**
     * @param string $userTag
     * @param int $authenticatedUserId
     * @param int $userToUnBanId
     * @return Follow
     * @throws BanException
     */
    private function checkUserGotBanned(string $userTag, int $authenticatedUserId, int $userToUnBanId): Follow
    {
        $userFollowStatus = $this->followRepository->checkPossibleToUnBan($authenticatedUserId, $userToUnBanId);
        if ($userFollowStatus === null) {
            throw new BanException(RedirectMessageEnum::USER_NOT_BANNED);
        }
        return $userFollowStatus;
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToBanId
     * @throws BanException
     */
    private function checkUserBanSelf(int $authenticatedUserId, int $userToBanId): void
    {
        if ($authenticatedUserId === $userToBanId) {
            throw new BanException(RedirectMessageEnum::BANNING_SELF_NOT_POSSIBLE);
        }
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToBanId
     * @throws BanException
     */
    private function checkUserUnBanSelf(int $authenticatedUserId, int $userToBanId): void
    {
        if ($authenticatedUserId === $userToBanId) {
            throw new BanException(RedirectMessageEnum::UNBANNING_SELF_NOT_POSSIBLE);
        }
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     * @return int
     */
    private function getNewBanStatus(Follow $userFollowStatus, int $authenticatedUserId): int
    {
        $status = $userFollowStatus->getAttribute('status');
        if ($userFollowStatus->getAttribute('user_id') === $authenticatedUserId) {
            $status = $userFollowStatus->getAttribute('status') | 1 << 2;
        }
        if ($userFollowStatus->getAttribute('follow_user_id') === $authenticatedUserId) {
            $status = $userFollowStatus->getAttribute('status') | 1 << 3;
        }
        return $status;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     * @return int
     */
    private function getNewUnBanStatus(Follow $userFollowStatus, int $authenticatedUserId): int
    {
        $status = $userFollowStatus->getAttribute('status');
        if ($userFollowStatus->getAttribute('user_id') === $authenticatedUserId) {
            $status = $userFollowStatus->getAttribute('status') ^ 1 << 2;
        }
        if ($userFollowStatus->getAttribute('follow_user_id') === $authenticatedUserId) {
            $status = $userFollowStatus->getAttribute('status') ^ 1 << 3;
        }
        return $status;
    }
}