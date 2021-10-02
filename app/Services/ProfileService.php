<?php

namespace App\Services;

use App\Repositories\{BanRepository, FollowRepository, NotificationRepository};

class ProfileService
{
    public function __construct(
        protected FollowRepository $followRepository,
        protected BanRepository $banRepository,
        protected NotificationRepository $notificationRepository
    )
    {
    }

    public function getStatusBetweenUsers(?int $authenticatedUserId, int $userId): array
    {
        $possibleFollow = false;
        $possibleUnFollow = false;
        $possibleBan = false;
        $possibleUnBan = false;
        $possibleNotificationsOn = false;
        $possibleNotificationsOff = false;
        if ($authenticatedUserId !== null) {
            if ($this->followRepository->getFollowRecord($authenticatedUserId, $userId) === null) {
                $possibleFollow = true;
                $possibleBan = true;
                $possibleNotificationsOn = true;
            }
            if (!$possibleFollow) {
                $possibleFollow = $this->getPossibleToFollow($authenticatedUserId, $userId);
                if (!$possibleFollow) {
                    $possibleUnFollow = $this->getPossibleToUnFollow($authenticatedUserId, $userId);
                }
            }
            if (!$possibleBan) {
                $possibleBan = $this->getPossibleToBan($authenticatedUserId, $userId);
                if (!$possibleBan) {
                    $possibleUnBan = $this->getPossibleToUnBan($authenticatedUserId, $userId);
                }
            }
            if (!$possibleNotificationsOn) {
                $possibleNotificationsOn = $this->getPossibleToTurnOnNotifications($authenticatedUserId, $userId);
                if (!$possibleNotificationsOn) {
                    $possibleNotificationsOff = $this->getPossibleToTurnOffNotifications($authenticatedUserId, $userId);
                }
            }
        }
        return [
            'possibleToFollow' => $possibleFollow,
            'possibleToUnFollow' => $possibleUnFollow,
            'possibleToBan' => $possibleBan,
            'possibleToUnBan' => $possibleUnBan,
            'possibleTurnOnNotifications' => $possibleNotificationsOn,
            'possibleTurnOffNotifications' => $possibleNotificationsOff,
        ];
    }

    private function getPossibleToFollow(int $authenticatedUserId, int $userId): bool
    {
        return $this->followRepository->checkPossibleToFollow($authenticatedUserId, $userId);
    }

    private function getPossibleToUnFollow(int $authenticatedUserId, int $userId): bool
    {
        return ($this->followRepository->getFollowStatusForUnFollow($authenticatedUserId, $userId) !== null);
    }

    private function getPossibleToBan(int $authenticatedUserId, int $userId): bool
    {
        return $this->banRepository->checkPossibleToBan($authenticatedUserId, $userId);
    }

    private function getPossibleToUnBan(int $authenticatedUserId, int $userId): bool
    {
        return ($this->banRepository->getFollowStatusForUnBan($authenticatedUserId, $userId) !== null);
    }

    private function getPossibleToTurnOnNotifications(int $authenticatedUserId, int $userId): bool
    {
        return $this->notificationRepository->checkPossibleToTurnOnNotifications(
            $authenticatedUserId,
            $userId
        );
    }

    private function getPossibleToTurnOffNotifications(int $authenticatedUserId, int $userId): bool
    {
        return (
            $this->notificationRepository->checkNotificationsAreTurnedOnForAuthenticatedUser(
                $authenticatedUserId,
                $userId
            ) !== null
        );
    }
}
