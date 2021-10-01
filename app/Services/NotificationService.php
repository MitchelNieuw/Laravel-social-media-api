<?php

namespace App\Services;

use App\Models\Follow;
use App\Exceptions\{NotificationException, UserException};
use App\Repositories\NotificationRepository;

class NotificationService
{
    use ServiceTrait;

    /**
     * @throws NotificationException
     * @throws UserException
     */
    public function turnOnNotifications(string $userTag, int $authenticatedUserId): string
    {
        $userFollowStatus = $this->getFollowStatusNotificationOnPossible(
            $authenticatedUserId,
            $this->checkUserExists($userTag)->id
        );
        if ($userFollowStatus !== null) {
            (new NotificationRepository())->updateFollow(
                $userFollowStatus,
                $this->getNewStatus($userFollowStatus, $authenticatedUserId, '|', 'notification')
            );
        }
        return 'Notifications are turned on';
    }

    /**
     * @throws NotificationException
     * @throws UserException
     */
    public function turnOffNotifications(string $userTag, int $authenticatedUserId): string
    {
        $followUserId = $this->checkUserExists($userTag)->id;
        $userFollowStatus = $this->getFollowStatusNotificationOffPossible($authenticatedUserId, $followUserId);
        if ($userFollowStatus !== null) {
            (new NotificationRepository())->updateFollow(
                $userFollowStatus,
                $this->getNewStatus($userFollowStatus, $authenticatedUserId, '^', 'notification')
            );
        }
        return 'Notifications are turned off';
    }

    /**
     * @throws NotificationException
     */
    private function getFollowStatusNotificationOnPossible(int $authenticatedUserId, int $followUserId): Follow
    {
        $userFollowStatus = (new NotificationRepository())->getFollowStatusForNotificationOn(
            $authenticatedUserId,
            $followUserId
        );
        if ($userFollowStatus === null) {
            throw new NotificationException('Cannot turn on notifications');
        }
        return $userFollowStatus;
    }

    /**
     * @throws NotificationException
     */
    private function getFollowStatusNotificationOffPossible(int $authenticatedUserId, int $followUserId): Follow
    {
        $userFollowStatus = (new NotificationRepository())->checkNotificationsAreTurnedOnForAuthenticatedUser(
            $authenticatedUserId,
            $followUserId
        );
        if ($userFollowStatus === null) {
            throw new NotificationException('Cannot turn on notifications');
        }
        return $userFollowStatus;
    }
}
