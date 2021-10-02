<?php

namespace App\Observers;

use App\Notifications\UserReactionNotification;
use App\Models\{Follow, Reaction, User};
use App\Repositories\{NotificationRepository, UserRepository};

class ReactionObserver
{
    public function created(Reaction $reaction): void
    {
        $this->notifyUserOfMessage($reaction);
    }

    private function notifyUserOfMessage(Reaction $reaction): void
    {
        $authenticatedUser = (new UserRepository)->getUserById($reaction->user_id);
        $userForNotification =  (new UserRepository)->getUserById($reaction->message->user_id);
        if (
            $authenticatedUser !== null
            && $userForNotification !== null
            && $this->checkSendNotificationIsPossible($userForNotification, $authenticatedUser) !== null
        ) {
            $userForNotification->notify(
                new UserReactionNotification($reaction->user->tag, $reaction->message_id)
            );
        }
    }

    private function checkSendNotificationIsPossible(User $userForNotification, User $authenticatedUser): ?Follow
    {
        return (new NotificationRepository)->checkNotificationsAreTurnedOnForAuthenticatedUser(
            $authenticatedUser->id,
            $userForNotification->id
        );
    }
}
