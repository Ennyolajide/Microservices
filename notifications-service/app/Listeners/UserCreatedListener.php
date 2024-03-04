<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;

class UserCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserCreated $event)
    {
        $userData = $event->user;
        // Log the sent user data
        File::append(storage_path('logs/user_events.log'), json_encode($userData) . PHP_EOL);
    }
}
