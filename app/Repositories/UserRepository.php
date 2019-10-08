<?php

namespace App\Repositories;

use App\User;

/**
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * @param string $userTag
     * @return User|null
     */
    public function getUserByUserTag(string $userTag): ?User
    {
        return User::where('tag', $userTag)->first();
    }

    /**
     * @param string $userTag
     * @return User|null
     */
    public function getUserByTagWithMessages(string $userTag): ?User
    {
        return User::where('tag', $userTag)->with('messages')->first();
    }
}