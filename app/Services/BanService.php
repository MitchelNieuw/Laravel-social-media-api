<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
use App\Exceptions\BanException;
use App\Exceptions\UserException;
use App\Follow;
use App\Repositories\BanRepository;
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
     * @var BanRepository
     */
    protected $banRepository;

    /**
     * @param UserRepository $userRepository
     * @param FollowRepository $followRepository
     * @param BanRepository $banRepository
     */
    public function __construct(
        UserRepository $userRepository,
        FollowRepository $followRepository,
        BanRepository $banRepository
    ) {
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
        $this->banRepository = $banRepository;
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
            $this->updateFollowRecordUser($userFollowStatus, $authenticatedUserId, '|');
        }
        return ResponseMessageEnum::BAN_SUCCESSFUL;
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
        $userFollowStatus = $this->checkUserGotBanned($authenticatedUserId, $userToUnBanId);
        $this->updateFollowRecordUser($userFollowStatus, $authenticatedUserId, '^');
        return ResponseMessageEnum::UNBAN_SUCCESSFUL;
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToBanId
     * @return Follow
     * @throws BanException
     */
    private function checkUserGotUnBanned(int $authenticatedUserId, int $userToBanId): Follow
    {
        $userFollowStatus = $this->banRepository->getFollowStatusForBan($authenticatedUserId, $userToBanId);
        if ($userFollowStatus === null) {
            throw new BanException(ResponseMessageEnum::USER_ALREADY_UNBANNED);
        }
        return $userFollowStatus;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     * @param string $operator
     */
    private function updateFollowRecordUser(Follow $userFollowStatus, int $authenticatedUserId, string $operator): void
    {
        if ($userFollowStatus !== null) {
            $status = $this->getNewStatus($userFollowStatus, $authenticatedUserId, $operator, 'ban');
            $this->followRepository->updateFollow($userFollowStatus, $status);
        }
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userToUnBanId
     * @return Follow
     * @throws BanException
     */
    private function checkUserGotBanned(int $authenticatedUserId, int $userToUnBanId): Follow
    {
        $userFollowStatus = $this->banRepository->getFollowStatusForUnBan($authenticatedUserId, $userToUnBanId);
        if ($userFollowStatus === null) {
            throw new BanException(ResponseMessageEnum::USER_NOT_BANNED);
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
            throw new BanException(ResponseMessageEnum::BANNING_SELF_NOT_POSSIBLE);
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
            throw new BanException(ResponseMessageEnum::UNBANNING_SELF_NOT_POSSIBLE);
        }
    }
}