<?php

namespace App\Repositories;

use App\Message;
use Exception;

/**
 * @package App\Repositories
 */
class MessageRepository
{
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
     * @param array $userIds
     * @param int $userId
     * @return array
     */
    private function removeStatusAndAuthenticatedUserIdFromArray(array $userIds, int $userId): array
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
     * @return mixed
     */
    public function getAllMessagesByUserId(int $userId)
    {
        return Message::where('user_id', $userId)->paginate(40);
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