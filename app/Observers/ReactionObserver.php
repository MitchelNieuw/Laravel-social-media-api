<?php

namespace App\Observers;

use App\Follow;
use App\Notifications\UserReactionNotification;
use App\Reaction;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;

/**
 * @package App\Observers
 */
class ReactionObserver
{
    /**
     * @param Reaction $reaction
     */
    public function created(Reaction $reaction): void
    {
        $this->notifyUserOfMessage($reaction);
    }

    /**
     * @param Reaction $reaction
     */
    private function notifyUserOfMessage(Reaction $reaction): void
    {
        $user = (new UserRepository())->getUserById($reaction->getAttribute('user_id'));
        if (
            ($user !== null)
            && $this->checkSendNotificationIsPossible($reaction, $user->getAttribute('id')) !== null
        ) {
            $user->notify(new UserReactionNotification($reaction->user->tag, $reaction->getAttribute('message_id')));
        }
    }

    /**
     * @param Reaction $reaction
     * @param int $authenticatedUserId
     * @return Follow|null
     */
    private function checkSendNotificationIsPossible(Reaction $reaction, int $authenticatedUserId): ?Follow
    {
        return (new NotificationRepository())->checkNotificationsAreTurnedOnForAuthenticatedUser(
            $reaction->message->user_id,
            $authenticatedUserId
        );
    }
}
