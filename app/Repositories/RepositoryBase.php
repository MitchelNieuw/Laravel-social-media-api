<?php

namespace App\Repositories;

use App\Follow;

/**
 * @package App\Repositories
 */
abstract class RepositoryBase
{
    /**
     * @param array $userIds
     * @param int $userId
     * @return array
     */
    public function removeStatusAndAuthenticatedUserIdFromArray(array $userIds, int $userId): array
    {
        foreach ($userIds as $key => $id) {
            unset($userIds[$key]['status']);
            if ($id['user_id'] === $userId) {
                unset($userIds[$key]['user_id']);
            }
            if ($id['follow_user_id'] === $userId) {
                unset($userIds[$key]['follow_user_id']);
            }
        }
        return $userIds;
    }

    /**
     * @param int $userId
     * @param int $followUserId
     * @param int $status
     * @return Follow
     */
    public function follow(int $userId, int $followUserId, int $status): Follow
    {
        return Follow::create([
            'user_id' => $userId,
            'follow_user_id' => $followUserId,
            'status' => $status,
        ]);
    }

    /**
     * @param Follow $follow
     * @param int $status
     * @return bool
     */
    public function updateFollow(Follow $follow, int $status): bool
    {
        return $follow->update([
            'status' => $status
        ]);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return Follow|null
     */
    protected function getFollowRecord(int $authenticatedUserId, int $followUserId): ?Follow
    {
        return Follow::where('user_id', $authenticatedUserId)
            ->where('follow_user_id', $followUserId)
            ->orWhere(static function ($query) use ($authenticatedUserId, $followUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->where('user_id', $followUserId);
            })
            ->first();
    }
}