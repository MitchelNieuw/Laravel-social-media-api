<?php

namespace App\Observers;

use App\Follow;
use App\Notifications\UserReactionNotification;
use App\Reaction;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use App\User;

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
        $authenticatedUser = (new UserRepository())->getUserById($reaction->getAttribute('user_id'));
        $userForNotification =  (new UserRepository())->getUserById($reaction->message->user_id);
        if (
            $authenticatedUser !== null
            && $userForNotification !== null
            && $this->checkSendNotificationIsPossible($userForNotification, $authenticatedUser) !== null
        ) {
            $userForNotification->notify(
                new UserReactionNotification($reaction->user->tag, $reaction->getAttribute('message_id'))
            );
        }
    }

    /**
     * @param User $userForNotification
     * @param User $authenticatedUser
     * @return Follow|null
     */
    private function checkSendNotificationIsPossible(User $userForNotification, User $authenticatedUser): ?Follow
    {
        return (new NotificationRepository())->checkNotificationsAreTurnedOnForAuthenticatedUser(
            $authenticatedUser->getAttribute('id'),
            $userForNotification->getAttribute('id')
        );
    }
}
