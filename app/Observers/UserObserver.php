<?php

namespace App\Observers;

use App\User;

/**
 * @package App\Observers
 */
class UserObserver
{
    /**
     * @param User $user
     */
    public function deleted(User $user): void
    {
        $user->messages()->delete();
        $user->reactions()->delete();
        $user->notifications()->delete();
    }
}
