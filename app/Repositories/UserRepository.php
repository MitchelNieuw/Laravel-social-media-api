<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function getUserByJwtToken(string $token): ?User
    {
        return User::where('jwt_token', $token)->first();
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
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
     * @param string $tag
     * @return Builder[]|Collection
     */
    public function searchForUsersInTagOrName(string $tag)
    {
        return User::where('tag', 'LIKE', '%' . $tag . '%')
            ->orWhere('name', 'LIKE', '%' . $tag . '%')
            ->paginate(10);
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
