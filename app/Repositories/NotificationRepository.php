<?php

namespace App\Repositories;

use App\Follow;

/**
 * @package App\Repositories
 */
class NotificationRepository extends RepositoryBase
{
    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return Follow
     */
    public function getFollowStatusForNotificationOn(int $authenticatedUserId, int $followUserId): Follow
    {
        return Follow::where('user_id', $authenticatedUserId)
            ->where('follow_user_id', $followUserId)
            ->whereRaw('status&1=1')
            ->whereRaw('NOT status&4=4')
            ->orWhere(static function ($query) use ($authenticatedUserId, $followUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->where('user_id', $followUserId)
                    ->whereRaw('status&2=2')
                    ->whereRaw('NOT status&8=8');
            })
            ->first();
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return Follow
     */
    public function getFollowStatusForNotificationOff(int $authenticatedUserId, int $followUserId): Follow
    {
        return Follow::where('user_id', $authenticatedUserId)
            ->where('follow_user_id', $followUserId)
            ->whereRaw('status&1=1')
            ->whereRaw('status&16=16')
            ->whereRaw('NOT status&4=4')
            ->orWhere(static function ($query) use ($authenticatedUserId, $followUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->where('user_id', $followUserId)
                    ->whereRaw('status&2=2')
                    ->whereRaw('status&32=32')
                    ->whereRaw('NOT status&8=8');
            })
            ->first();
    }

    /**
     * @param int $authenticatedUserId
     * @return array
     */
    public function getUserIdsWhereNotificationsArePossible(int $authenticatedUserId): array
    {
        return Follow::select('user_id', 'follow_user_id', 'status')
            ->where('user_id', $authenticatedUserId)
            ->whereRaw('status&2=2')
            ->whereRaw('status&32=32')
            ->whereRaw('NOT status&8=8')
            ->orWhere(static function ($query) use ($authenticatedUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->whereRaw('status&1=1')
                    ->whereRaw('status&16=16')
                    ->whereRaw('NOT status&4=4');
            })
            ->get()
            ->toArray();
    }
}