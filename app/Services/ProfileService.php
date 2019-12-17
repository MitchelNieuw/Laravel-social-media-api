<?php

namespace App\Services;

use App\Repositories\BanRepository;
use App\Repositories\FollowRepository;
use App\Repositories\MessageRepository;
use App\Repositories\NotificationRepository;
use App\User;
use Illuminate\View\View;

/**
 * @package App\Services
 */
class ProfileService
{
    use ServiceTrait;

    /**
     * @var FollowRepository
     */
    protected $followRepository;

    /**
     * @var BanRepository
     */
    protected $banRepository;

    /**
     * @var NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @param FollowRepository $followRepository
     * @param BanRepository $banRepository
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        FollowRepository $followRepository,
        BanRepository $banRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->followRepository = $followRepository;
        $this->banRepository = $banRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @return View
     */
    public function displayProfile(): View
    {
        $userId = auth()->user()->getAuthIdentifier();
        $messages = (new MessageRepository())->getMessagesByUserId($userId);
        $followingCount = $this->followRepository->getFollowingCount($userId);
        $followersCount = $this->followRepository->getFollowersCount($userId);
        return view('profile', compact('messages', 'followingCount', 'followersCount'));
    }

    /**
     * @param User $user
     * @return array
     */
    public function displayUser(User $user): array
    {
        $userId = $user->getAttribute('id');
        $authenticatedUserId = null;
        if (auth()->user() !== null) {
            $authenticatedUserId = auth()->user()->getAuthIdentifier();
        }
        $arrayStatus = $this->getStatusBetweenUsers($authenticatedUserId, $userId);
        return [
            'user' => $user,
            'messages' => $user->messages()->paginate(20),
            'possibleFollow' => $arrayStatus['possibleToFollow'],
            'possibleUnFollow' => $arrayStatus['possibleToUnFollow'],
            'possibleBan' => $arrayStatus['possibleToBan'],
            'possibleUnBan' => $arrayStatus['possibleToUnBan'],
            'possibleTurnOnNotifications' => $arrayStatus['possibleTurnOnNotifications'],
            'possibleTurnOffNotifications' => $arrayStatus['possibleTurnOffNotifications'],
            'following' => $this->followRepository->getFollowingCount($userId),
            'followers' => $this->followRepository->getFollowersCount($userId),
        ];
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return array
     */
    public function getStatusBetweenUsers(int $authenticatedUserId, int $userId): array
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

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function getPossibleToFollow(int $authenticatedUserId, int $userId): bool
    {
        return $this->followRepository->checkPossibleToFollow($authenticatedUserId, $userId);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function getPossibleToUnFollow(int $authenticatedUserId, int $userId): bool
    {
        return ($this->followRepository->getFollowStatusForUnFollow($authenticatedUserId, $userId) !== null);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function getPossibleToBan(int $authenticatedUserId, int $userId): bool
    {
        return $this->banRepository->checkPossibleToBan($authenticatedUserId, $userId);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function getPossibleToUnBan(int $authenticatedUserId, int $userId): bool
    {
        return ($this->banRepository->getFollowStatusForUnBan($authenticatedUserId, $userId) !== null);
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function getPossibleToTurnOnNotifications(int $authenticatedUserId, int $userId): bool
    {
        return $this->notificationRepository->checkPossibleToTurnOnNotifications(
            $authenticatedUserId,
            $userId
        );
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function getPossibleToTurnOffNotifications(int $authenticatedUserId, int $userId): bool
    {
        return (
            $this->notificationRepository->checkNotificationsAreTurnedOnForAuthenticatedUser($authenticatedUserId, $userId) !== null
        );
    }
}