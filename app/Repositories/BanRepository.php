<?php

namespace App\Repositories;

use App\Models\Follow;

class BanRepository extends RepositoryBase
{
    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return bool
     */
    public function checkPossibleToBan(int $authenticatedUserId, int $followUserId): bool
    {
        return ($this->getFollowStatusForBan($authenticatedUserId, $followUserId) !== null);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return Follow|null
     */
    public function getFollowStatusForBan(int $authenticatedUserId, int $followUserId): ?Follow
    {
        return Follow::where('user_id', $authenticatedUserId)
            ->where('follow_user_id', $followUserId)
            ->whereRaw('NOT status&4=4')
            ->orWhere(static function ($query) use ($authenticatedUserId, $followUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->where('user_id', $followUserId)
                    ->whereRaw('NOT status&8=8');
            })
            ->first();
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return Follow|null
     */
    public function getFollowStatusForUnBan(int $authenticatedUserId, int $followUserId): ?Follow
    {
        return Follow::where('user_id', $authenticatedUserId)
            ->where('follow_user_id', $followUserId)
            ->whereRaw('status&4=4')
            ->orWhere(static function ($query) use ($authenticatedUserId, $followUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->where('user_id', $followUserId)
                    ->whereRaw('status&8=8');
            })
            ->first();
    }
}
