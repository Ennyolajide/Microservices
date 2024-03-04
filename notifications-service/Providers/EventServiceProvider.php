<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\UserCreated;
use App\Listeners\UserCreatedListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserCreated::class => [
            UserCreatedListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
