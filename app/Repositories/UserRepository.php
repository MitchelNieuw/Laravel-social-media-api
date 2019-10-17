<?php

namespace App\Repositories;

use App\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * @param string $token
     * @return User|null
     */
    public function getUserByJwtToken(string $token): ?User
    {
        return User::where('jwt_token', $token)->first();
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * @param array $userIds
     * @return Collection
     */
    public function getUsersByIds(array $userIds): Collection
    {
        return User::whereIn('id', $userIds)->get();
    }

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