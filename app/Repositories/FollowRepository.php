<?php

namespace App\Repositories;

use App\Follow;
use Illuminate\Database\Eloquent\Collection;

/**
 * @package App\Repositories
 */
class FollowRepository
{
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
    public function getFollowStatusForFollow(int $authenticatedUserId, int $followUserId): ?Follow
    {
        return Follow::where('user_id', $authenticatedUserId)
            ->where('follow_user_id', $followUserId)
            ->whereRaw('NOT (status&1=1 OR status&4=4)')
            ->orWhere(static function ($query) use ($authenticatedUserId, $followUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->where('user_id', $followUserId)
                    ->whereRaw('NOT (status&2=2 OR status&8=8)');
            })
            ->first();
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
     * @return bool
     */
    public function checkPossibleToFollow(int $authenticatedUserId, int $followUserId): bool
    {
        if ($this->getFollowRecord($authenticatedUserId, $followUserId) === null) {
            return true;
        }
        $follow = $this->getFollowStatusForFollow($authenticatedUserId, $followUserId);
        return ($follow !== null);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return bool
     */
    public function checkPossibleToBan(int $authenticatedUserId, int $followUserId): bool
    {
        if ($this->getFollowRecord($authenticatedUserId, $followUserId) === null) {
            return true;
        }
        $follow = $this->getFollowStatusForBan($authenticatedUserId, $followUserId);
        return ($follow !== null);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserIdsOfFollowingUsers(int $userId): array
    {
        return $this->getFollowing($userId)->get()->toArray();
    }

    /**
     * @param int $userId
     * @return int
     */
    public function getFollowingCount(int $userId): int
    {
        return $this->getFollowing($userId)->count();
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getFollowingUsersWithRelationships(int $userId): Collection
    {
        return $this->getFollowing($userId)
            ->with('user:id,name,tag,profilePicture', 'following:id,name,tag,profilePicture')
            ->get();
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return Follow|null
     */
    public function getFollowStatusForUnFollow(int $authenticatedUserId, int $followUserId): ?Follow
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
     * @return Follow|null
     */
    public function checkPossibleToUnBan(int $authenticatedUserId, int $followUserId): ?Follow
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

    /**
     * @param int $userId
     * @return int
     */
    public function getFollowersCount(int $userId): int
    {
        return $this->getFollowers($userId)->count();
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getFollowersWithRelationsByUserId(int $userId): Collection
    {
        return $this->getFollowers($userId)
            ->with('user:id,name,tag,profilePicture', 'following:id,name,tag,profilePicture')
            ->get();
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return Follow|null
     */
    private function getFollowRecord(int $authenticatedUserId, int $followUserId): ?Follow
    {
        return Follow::where('user_id', $authenticatedUserId)
            ->where('follow_user_id', $followUserId)
            ->orWhere(static function ($query) use ($authenticatedUserId, $followUserId) {
                return $query->where('follow_user_id', $authenticatedUserId)
                    ->where('user_id', $followUserId);
            })
            ->first();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    private function getFollowing(int $userId)
    {
        return Follow::select('user_id', 'follow_user_id', 'status')
            ->where('user_id', $userId)
            ->whereRaw('status&1=1')
            ->whereRaw('NOT status&4=4')
            ->orWhere(static function ($query) use ($userId) {
                return $query->where('follow_user_id', $userId)
                    ->whereRaw('status&2=2')
                    ->whereRaw('NOT status&8=8');
            });
    }

    /**
     * @param int $userId
     * @return mixed
     */
    private function getFollowers(int $userId)
    {
        return Follow::where('user_id', $userId)
            ->whereRaw('status&2=2')
            ->whereRaw('NOT status&8=8')
            ->orWhere(static function ($query) use ($userId) {
                return $query->where('follow_user_id', $userId)
                    ->whereRaw('status&1=1')
                    ->whereRaw('NOT status&4=4');
            });
    }
}
