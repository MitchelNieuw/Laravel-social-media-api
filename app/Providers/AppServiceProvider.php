<?php

namespace App\Providers;

use App\Observers\{MessageObserver, ReactionObserver};
use App\Models\{Message, Reaction};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Message::observe(MessageObserver::class);
        Reaction::observe(ReactionObserver::class);
    }
}
