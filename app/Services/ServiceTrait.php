<?php


namespace App\Services;

use App\Enums\FollowEnum;
use App\Exceptions\UserException;
use App\Repositories\{FollowRepository, UserRepository};
use App\Models\{Follow, User};

trait ServiceTrait
{
    /**
     * @throws UserException
     */
    protected function checkUserExists(string $userTag): User
    {
        if (($user = (new UserRepository())->getUserByUserTag($userTag)) === null) {
            throw new UserException('User with the tag ' . $userTag. ' doesnt exist');
        }
        return $user;
    }

    protected function createFollowStatusRecord(
        ?Follow $userFollowStatus,
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

    protected function getNewStatus(Follow $userFollowStatus, int $userId, string $operator, string $type): int
    {
        $status = $userFollowStatus->status;
        $newBitArray = $this->getBitValueOnType($type);
        $followStatusUserId = $userFollowStatus->user_id;
        $followStatusFollowUserId = $userFollowStatus->follow_user_id;
        switch ($operator):
            case ('|'):
                if ($followStatusUserId === $userId) {
                    $status |= $newBitArray[0];
                }
                if ($followStatusFollowUserId === $userId) {
                    $status |= $newBitArray[1];
                }
                break;
            case ('^'):
                if ($followStatusUserId === $userId) {
                    $status ^= $newBitArray[0];
                }
                if ($followStatusFollowUserId === $userId) {
                    $status ^= $newBitArray[1];
                }
                break;
        endswitch;
        return $status;
    }

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
