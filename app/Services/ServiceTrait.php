<?php


namespace App\Services;

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
        $userToUnBan = (new UserRepository())->getUserByUserTag($userTag);
        if ($userToUnBan === null) {
            throw new UserException('User with the tag ' . $userTag. ' doesnt exist');
        }
        return $userToUnBan;
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
}