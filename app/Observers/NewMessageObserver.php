<?php

namespace App\Observers;

use App\Message;
use App\Notifications\UserNewMessageNotification;
use App\Notifications\UserTaggedInMessage;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Support\Facades\DB;

/**
 * @package App\Observers
 */
class NewMessageObserver
{
    /**
     * @param Message $message
     */
    public function created(Message $message): void
    {
        $allTaggedUserIds = $this->notifyAllExistingTaggedUsers($message);
        $this->notifyUsersWhenNotTagged($message, $allTaggedUserIds);
    }

    /**
     * @param Message $message
     */
    public function deleted(Message $message): void
    {
        DB::table('notifications')->where('data', 'like', '%\"messageId\":'.$message->id.'%' )->delete();
    }

    /**
     * @param Message $message
     * @return array
     */
    private function notifyAllExistingTaggedUsers(Message $message): array
    {
        $messageContent = $message->getAttribute('content');
        $authenticatedUserId = $message->user->id;
        $arrayString = explode(' ', $messageContent);
        $allTaggedUserIds = [];
        foreach ($arrayString as $item) {
            if (strpos($item, '@') !== false) {
                $taggedUser = preg_replace('/@/', '', $item);
                $user = (new UserRepository())->getUserByUserTag($taggedUser);
                $allTaggedUserIds[] = $this->userTaggedInMessageExistsSendNotification(
                    $user,
                    $authenticatedUserId,
                    $message
                );
            }
            if (count($allTaggedUserIds) === env('MAX_TAG_ABLE_USERS_IN_MESSAGE')) {
                break;
            }
        }
        return $allTaggedUserIds;
    }


    /**
     * @param User|null $user
     * @param int $authenticatedUserId
     * @param Message $message
     * @return int|void
     */
    private function userTaggedInMessageExistsSendNotification(
        ?User $user,
        int $authenticatedUserId,
        Message $message
    ) {
        if ($user !== null) {
            $taggedUserId = $user->getAttribute('id');
            if ($this->checkNotificationPossibleToSend($user, $taggedUserId, $authenticatedUserId)) {
                $user->notify(new UserTaggedInMessage($message->user->tag, $message));
                return $taggedUserId;
            }
        }
    }

    /**
     * @param User $user
     * @param int $taggedUserId
     * @param int $authenticatedUserId
     * @return bool
     */
    private function checkNotificationPossibleToSend(User $user, int $taggedUserId, int $authenticatedUserId): bool
    {
        $query = $user->following()
            ->where('user_id', $taggedUserId)
            ->where('follow_user_id', $authenticatedUserId)
            ->whereRaw('status&16=16')
            ->whereRaw('NOT status&4=4')
            ->orWhere(static function ($query) use ($authenticatedUserId, $taggedUserId) {
                return $query->where('user_id', $authenticatedUserId)
                    ->where('follow_user_id', $taggedUserId)
                    ->whereRaw('status&32=32')
                    ->whereRaw('NOT status&8=8');
            })
            ->first();
        return !($query === null);
    }

    /**
     * @param Message $message
     * @param array $allTaggedUserIds
     */
    private function notifyUsersWhenNotTagged(Message $message, array $allTaggedUserIds): void
    {
        $users = (new UserRepository())->getUsersByIds($this->getUserIdsForNotifications($message->user->id));
        if (!$users->isEmpty()) {
            foreach ($users as $user) {
                if (!in_array((int)$user->getAttribute('id'), $allTaggedUserIds, true)) {
                    $user->notify(new UserNewMessageNotification($message->user->tag, $message->id));
                }
            }
        }
    }

    /**
     * @param int $authenticatedUserId
     * @return array
     */
    private function getUserIdsForNotifications(int $authenticatedUserId): array
    {
        $notificationRepository = (new NotificationRepository());
        return $notificationRepository->removeStatusAndAuthenticatedUserIdFromArray(
            $notificationRepository->getUserIdsWhereNotificationsArePossible($authenticatedUserId),
            $authenticatedUserId
        );
    }
}
