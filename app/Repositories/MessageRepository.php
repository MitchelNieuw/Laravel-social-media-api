<?php

namespace App\Repositories;

use App\Models\Message;
use Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * @package App\Repositories
 */
class MessageRepository extends RepositoryBase
{
    /**
     * @param int $userId
     * @return Collection
     */
    public function getMessagesFromFollowingUsers(int $userId): Collection
    {
        $userIds = $this->removeStatusAndAuthenticatedUserIdFromArray(
            (new FollowRepository)->getUserIdsOfFollowingUsers($userId),
            $userId
        );
        return Message::with('user:id,name,tag,profile_picture', 'reactions', 'reactions.user')
            ->whereIn('user_id', $userIds)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @param int $userId
     * @return Collection|null
     */
    public function getMessagesByUserId(int $userId): ?Collection
    {
        return Message::where('user_id', $userId)
            ->with('reactions', 'reactions.user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }


    /**
     * @param int $id
     * @return Message|null
     */
    public function findById(int $id): ?Message
    {
        return Message::find($id);
    }

    /**
     * @param Message $message
     * @return bool|null
     * @throws Exception
     */
    public function delete(Message $message): ?bool
    {
        return $message->delete();
    }
}
