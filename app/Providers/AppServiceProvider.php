<?php

namespace App\Providers;

use App\Observers\{MessageObserver, ReactionObserver, UserObserver};
use App\Models\{Message, Reaction, User};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Message::observe(MessageObserver::class);
        Reaction::observe(ReactionObserver::class);
        User::observe(UserObserver::class);
    }
}
