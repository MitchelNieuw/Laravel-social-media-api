<?php

namespace App\Observers;

use App\Message;
use App\Notifications\UserNewMessageNotification;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;

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
        $data = $message->getAttribute('content');
        $arrayString = explode(' ', $data);
        $allTaggedPersons = [];
        foreach ($arrayString as $item) {
            if (strpos($item, '@') !== false) {
                $allTaggedPersons[] = preg_replace('/@/', '', $item);
            }
        }
        dd($allTaggedPersons);

        // TODO: send notification if tagged person has not banned user that sends message
        // TODO: check if user tags next to each other without space in between
        $users = (new UserRepository())->getUsersByIds($this->getUserIdsForNotifications($message->user->id));
        if (!$users->isEmpty()) {
            foreach ($users as $user) {
                $user->notify(new UserNewMessageNotification($message->user->tag, $message->id));
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
