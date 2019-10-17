<?php

namespace App\Repositories;

use App\Message;
use Exception;

/**
 * @package App\Repositories
 */
class MessageRepository extends RepositoryBase
{
    /**
     * @param int $id
     * @return Message|null
     */
    public function getMessageById(int $id): ?Message
    {
        return Message::find($id);
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getMessagesFromFollowingUsers(int $userId)
    {
        $userIds = $this->removeStatusAndAuthenticatedUserIdFromArray(
            (new FollowRepository())->getUserIdsOfFollowingUsers($userId),
            $userId
        );
        return Message::with('user:id,name,tag,profilePicture')->whereIn('user_id', $userIds)
            ->orderByDesc('created_at')->paginate(40);
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getAllMessagesByUserId(int $userId)
    {
        return Message::where('user_id', $userId)->orderBy('created_at', 'DESC')->paginate(40);
    }

    /**
     * @param int $id
     * @return Message
     */
    public function findById(int $id): Message
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