<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Events\UserCreated;
use App\Listeners\UserCreatedListener;
use Illuminate\Support\Facades\File;

class UserCreatedListenerTest extends TestCase
{
    public function test_user_created_listener()
    {
        // Mock the File facade to prevent actual file writes
        File::shouldReceive('append')->once()->andReturnNull();

        // Create a sample user data
        $userData = ['id' => 1, 'name' => 'Test User'];

        // Create a new instance of the listener
        $listener = new UserCreatedListener();

        // Fire the UserCreated event and pass the user data
        $event = new UserCreated($userData);

        // Call the handle method of the listener manually
        $listener->handle($event);

        // Assert that the File::append method was called with the correct arguments
        File::shouldHaveReceived('append')->once()->with(
            storage_path('logs/user_events.log'),
            json_encode($userData) . PHP_EOL
        );
    }
}
