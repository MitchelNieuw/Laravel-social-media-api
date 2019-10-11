<?php

namespace App\Services;

use App\Repositories\BanRepository;
use App\Repositories\FollowRepository;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
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
     * @param FollowRepository $followRepository
     * @param BanRepository $banRepository
     */
    public function __construct(FollowRepository $followRepository, BanRepository $banRepository)
    {
        $this->followRepository = $followRepository;
        $this->banRepository = $banRepository;
    }

    /**
     * @return View
     */
    public function displayProfile(): View
    {
        $userId = auth()->user()->getAuthIdentifier();
        $messages = (new MessageRepository())->getAllMessagesByUserId($userId);
        $followingCount = $this->followRepository->getFollowingCount($userId);
        $followersCount = $this->followRepository->getFollowersCount($userId);
        return view('profile', compact('messages', 'followingCount', 'followersCount'));
    }

    /**
     * @param string $userTag
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function displayUser(string $userTag)
    {
        $user = (new UserRepository())->getUserByTagWithMessages($userTag);
        if ($user === null) {
            return back();
        }
        $userId = $user->getAttribute('id');
        $possibleFollow = false;
        $possibleUnFollow = false;
        $possibleBan = false;
        $possibleUnBan = false;
        if (auth()->user() !== null) {
            $authenticatedUserId = auth()->user()->getAuthIdentifier();
            if ($authenticatedUserId === $userId) {
                return redirect('profile');
            }
            $possibleFollow = $this->followRepository->checkPossibleToFollow($authenticatedUserId, $userId);
            $possibleUnFollow = (
                $this->followRepository->getFollowStatusForUnFollow($authenticatedUserId, $userId) !== null
            );
            $possibleBan = $this->banRepository->checkPossibleToBan($authenticatedUserId, $userId);
            $possibleUnBan = ($this->banRepository->checkPossibleToUnBan($authenticatedUserId, $userId) !== null);
        }
        $messages = $user->messages()->paginate(20);
        $following = $this->followRepository->getFollowingCount($userId);
        $followers = $this->followRepository->getFollowersCount($userId);
        return view('user',
            compact(
                'user',
                'messages',
                'possibleFollow',
                'possibleUnFollow',
                'possibleBan',
                'possibleUnBan',
                'following',
                'followers',
            )
        );
    }
}