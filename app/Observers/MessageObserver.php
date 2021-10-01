<?php

namespace App\Observers;

use App\Models\Message;
use App\Notifications\{UserNewMessageNotification, UserTaggedInMessage};
use App\Models\Reaction;
use App\Repositories\{NotificationRepository, UserRepository};
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessageObserver
{
    public function created(Message $message): void
    {
        $this->notifyUsersWhenNotTagged($message, $this->notifyAllExistingTaggedUsers($message));
    }

    public function deleted(Message $message): void
    {
        DB::table('notifications')->where('data', 'like', '%\"messageId\":' . $message->id . '%')->delete();
        Reaction::where('message_id', $message->id)->delete();
    }

    private function notifyAllExistingTaggedUsers(Message $message): array
    {
        $messageContent = $message->getAttribute('content');
        $authenticatedUserId = $message->user->id;
        $arrayString = explode(' ', $messageContent);
        $allTaggedUserIds = [];
        foreach ($arrayString as $item) {
            if (str_contains($item, '@')) {
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
            $taggedUserId = $user->id;
            if ($this->checkNotificationPossibleToSend($user, $taggedUserId, $authenticatedUserId)) {
                $user->notify(new UserTaggedInMessage($message->user->tag, $message));
                return $taggedUserId;
            }
        }
    }

    private function checkNotificationPossibleToSend(
        User $user,
        int $taggedUserId,
        int $authenticatedUserId
    ): bool {
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

    private function notifyUsersWhenNotTagged(Message $message, array $allTaggedUserIds): void
    {
        $users = (new UserRepository)->getUsersByIds($this->getUserIdsForNotifications($message->user->id));
        if ($users->isNotEmpty()) {
            foreach ($users as $user) {
                if (!in_array((int)$user->id, $allTaggedUserIds, true)) {
                    $user->notify(new UserNewMessageNotification($message->user->tag, $message->id));
                }
            }
        }
    }

    private function getUserIdsForNotifications(int $authenticatedUserId): array
    {
        $notificationRepository = (new NotificationRepository);
        return $notificationRepository->removeStatusAndAuthenticatedUserIdFromArray(
            $notificationRepository->getUserIdsWhereNotificationsArePossible($authenticatedUserId),
            $authenticatedUserId
        );
    }
}
