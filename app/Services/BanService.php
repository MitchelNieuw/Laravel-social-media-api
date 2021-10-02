<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
use App\Exceptions\{BanException, UserException};
use App\Models\Follow;
use App\Repositories\{BanRepository, FollowRepository, UserRepository};

class BanService
{
    use ServiceTrait;

    public function __construct(
        protected UserRepository $userRepository,
        protected FollowRepository $followRepository,
        protected BanRepository $banRepository
    )
    {
    }

    /**
     * @throws BanException
     * @throws UserException
     */
    public function banUserByTag(string $userTag, int $authenticatedUserId): string
    {
        $userToBanId = $this->checkUserExists($userTag)->id;
        $this->checkUserBanSelf($authenticatedUserId, $userToBanId);
        $userFollowStatus = $this->checkUserGotUnBanned($authenticatedUserId, $userToBanId);
        if (!$this->createFollowStatusRecord($userFollowStatus, $authenticatedUserId, $userToBanId, 4)){
            $this->updateFollowRecordUser($userFollowStatus, $authenticatedUserId, '|');
        }
        return ResponseMessageEnum::BAN_SUCCESSFUL;
    }

    /**
     * @throws BanException
     * @throws UserException
     */
    public function unBanByUserTag(string $userTag, int $authenticatedUserId): string
    {
        $userToUnBanId = $this->checkUserExists($userTag)->id;
        $this->checkUserUnBanSelf($authenticatedUserId, $userToUnBanId);
        $userFollowStatus = $this->checkUserGotBanned($authenticatedUserId, $userToUnBanId);
        $this->updateFollowRecordUser($userFollowStatus, $authenticatedUserId, '^');
        return ResponseMessageEnum::UNBAN_SUCCESSFUL;
    }

    /**
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

    private function updateFollowRecordUser(
        ?Follow $userFollowStatus,
        int $authenticatedUserId,
        string $operator
    ): void
    {
        if ($userFollowStatus !== null) {
            $status = $this->getNewStatus($userFollowStatus, $authenticatedUserId, $operator, 'ban');
            $this->followRepository->updateFollow($userFollowStatus, $status);
        }
    }

    /**
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
     * @throws BanException
     */
    private function checkUserBanSelf(int $authenticatedUserId, int $userToBanId): void
    {
        if ($authenticatedUserId === $userToBanId) {
            throw new BanException(ResponseMessageEnum::BANNING_SELF_NOT_POSSIBLE);
        }
    }

    /**
     * @throws BanException
     */
    private function checkUserUnBanSelf(int $authenticatedUserId, int $userToBanId): void
    {
        if ($authenticatedUserId === $userToBanId) {
            throw new BanException(ResponseMessageEnum::UNBANNING_SELF_NOT_POSSIBLE);
        }
    }
}
