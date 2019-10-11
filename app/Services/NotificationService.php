<?php

namespace App\Services;

use App\Enums\FollowEnum;
use App\Exceptions\NotificationException;
use App\Exceptions\UserException;
use App\Follow;
use App\Repositories\NotificationRepository;

/**
 * @package App\Services
 */
class NotificationService
{
    use ServiceTrait;

    /**
     * @param string $userTag
     * @return string
     * @throws NotificationException
     * @throws UserException
     */
    public function turnOnNotifications(string $userTag): string
    {
        $authenticatedUserId = auth()->user()->getAuthIdentifier();
        $followUser = $this->checkUserExists($userTag);
        $followUserId = $followUser->getAttribute('id');
        $userFollowStatus = $this->getFollowStatusNotificationOnPossible($authenticatedUserId, $followUserId);
        if ($userFollowStatus !== null) {
            (new NotificationRepository())->updateFollow(
                $userFollowStatus,
                $this->getNewStatus($userFollowStatus, $authenticatedUserId, '|', 'notification')
            );
        }
        return '';
    }

    /**
     * @param string $userTag
     * @return string
     * @throws NotificationException
     * @throws UserException
     */
    public function turnOffNotifications(string $userTag): string
    {
        $authenticatedUserId = auth()->user()->getAuthIdentifier();
        $followUser = $this->checkUserExists($userTag);
        $followUserId = $followUser->getAttribute('id');
        $userFollowStatus = $this->getFollowStatusNotificationOffPossible($authenticatedUserId, $followUserId);
        if ($userFollowStatus !== null) {
            (new NotificationRepository())->updateFollow(
                $userFollowStatus,
                $this->getNewStatus($userFollowStatus, $authenticatedUserId, '^', 'notification')
            );
        }
        return '';
    }

    /**
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return mixed
     * @throws NotificationException
     */
    private function getFollowStatusNotificationOnPossible(int $authenticatedUserId, int $followUserId)
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
     * @param int $authenticatedUserId
     * @param int $followUserId
     * @return mixed
     * @throws NotificationException
     */
    private function getFollowStatusNotificationOffPossible(int $authenticatedUserId, int $followUserId)
    {
        $userFollowStatus = (new NotificationRepository())->getFollowStatusForNotificationOff(
            $authenticatedUserId,
            $followUserId
        );
        if ($userFollowStatus === null) {
            throw new NotificationException('Cannot turn on notifications');
        }
        return $userFollowStatus;
    }
}