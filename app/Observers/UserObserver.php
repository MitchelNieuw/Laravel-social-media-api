<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function deleted(User $user): void
    {
        $user->messages()->each(function ($message) {
            $message->delete();
        });
        $user->reactions()->each(function ($reaction) {
            $reaction->delete();
        });
        $user->followed()->each(function ($follower) {
            $follower->delete();
        });
        $user->following()->each(function ($follower) {
            $follower->delete();
        });
    }
}
