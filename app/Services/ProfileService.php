<?php

namespace App\Services;

use App\Exceptions\FollowException;
use App\Exceptions\UserException;
use App\Repositories\FollowRepository;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
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
     * @return View
     */
    public function displayProfile(): View
    {
        $userId = auth()->user()->getAuthIdentifier();
        $messages = (new MessageRepository())->getAllMessagesByUserId($userId);
        $followingCount = (new FollowRepository())->getFollowingCount($userId);
        $followersCount = (new FollowRepository())->getFollowersCount($userId);
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
            $possibleFollow = (new FollowRepository())->checkPossibleToFollow($authenticatedUserId, $userId);
            $possibleUnFollow = $this->checkUnFollowIsPossible($authenticatedUserId, $userId);
            $possibleBan = (new FollowRepository())->checkPossibleToBan($authenticatedUserId, $userId);
            $possibleUnBan = $this->checkUnBanIsPossible($authenticatedUserId, $userId);
        }
        $messages = $user->messages()->paginate(20);
        $following = (new FollowRepository())->getFollowingCount($userId);
        $followers = (new FollowRepository())->getFollowersCount($userId);
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

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function checkUnFollowIsPossible(int $authenticatedUserId, int $userId): bool
    {
        return (new FollowRepository())->getFollowStatusForUnFollow($authenticatedUserId, $userId) !== null;
    }

    /**
     * @param int $authenticatedUserId
     * @param int $userId
     * @return bool
     */
    private function checkUnBanIsPossible(int $authenticatedUserId, int $userId): bool
    {
        return (new FollowRepository())->checkPossibleToUnBan($authenticatedUserId, $userId) !== null;
    }
}