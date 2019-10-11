<?php


namespace App\Services;

use App\Enums\FollowEnum;
use App\Exceptions\UserException;
use App\Follow;
use App\Repositories\FollowRepository;
use App\Repositories\UserRepository;
use App\User;

/**
 * @package App\Services
 */
trait ServiceTrait
{
    /**
     * @param string $userTag
     * @return User
     * @throws UserException
     */
    protected function checkUserExists(string $userTag): User
    {
        $user = (new UserRepository())->getUserByUserTag($userTag);
        if ($user === null) {
            throw new UserException('User with the tag ' . $userTag. ' doesnt exist');
        }
        return $user;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $authenticatedUserId
     * @param int $userId
     * @param int $status
     * @return bool
     */
    protected function createFollowStatusRecord(
        Follow $userFollowStatus,
        int $authenticatedUserId,
        int $userId,
        int $status
    ): bool {
        if ($userFollowStatus === null) {
            (new FollowRepository())->follow($authenticatedUserId, $userId, $status);
            return true;
        }
        return false;
    }

    /**
     * @param Follow $userFollowStatus
     * @param int $userId
     * @param string $operator
     * @param string $type
     * @return int
     */
    protected function getNewStatus(Follow $userFollowStatus, int $userId, string $operator, string $type): int
    {
        $status = $userFollowStatus->getAttribute('status');
        $newBitArray = $this->getBitValueOnType($type);
        switch ($operator):
            case ('|'):
                if ($userFollowStatus->getAttribute('user_id') === $userId) {
                    $status = $userFollowStatus->getAttribute('status') | $newBitArray[0];
                }
                if ($userFollowStatus->getAttribute('follow_user_id') === $userId) {
                    $status = $userFollowStatus->getAttribute('status') | $newBitArray[1];
                }
                break;
            case ('^'):
                if ($userFollowStatus->getAttribute('user_id') === $userId) {
                    $status = $userFollowStatus->getAttribute('status') ^ $newBitArray[0];
                }
                if ($userFollowStatus->getAttribute('follow_user_id') === $userId) {
                    $status = $userFollowStatus->getAttribute('status') ^ $newBitArray[1];
                }
                break;
        endswitch;
        return $status;
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getBitValueOnType(string $type): array
    {
        $userIdNewBit = 0;
        $followUserIdNewBit = 0;
        switch ($type):
            case ('follow'):
                $userIdNewBit = FollowEnum::USER1_FOLLOWS_USER2;
                $followUserIdNewBit = FollowEnum::USER2_FOLLOWS_USER1;
                break;
            case ('ban'):
                $userIdNewBit = FollowEnum::USER1_BANNED_USER2;
                $followUserIdNewBit = FollowEnum::USER2_BANNED_USER1;
                break;
            case ('notification'):
                $userIdNewBit = FollowEnum::USER1_NOTIFICATIONS_ON_FOR_USER2;
                $followUserIdNewBit = FollowEnum::USER2_NOTIFICATIONS_ON_FOR_USER1;
                break;
        endswitch;
        return [
            $userIdNewBit,
            $followUserIdNewBit
        ];
    }
}