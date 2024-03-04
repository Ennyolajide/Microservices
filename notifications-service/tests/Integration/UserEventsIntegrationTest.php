<?php

namespace Tests\Integration;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Event;
use App\Listeners\UserCreatedListener;

class UserEventsIntegrationTest extends TestCase
{

    /** @test */
    public function test_handles_user_events_properly()
    {
        // Fake event dispatching
        Event::fake();
        $listener = new UserCreatedListener();
        File::shouldReceive('append')->once()->andReturnNull();

        // Simulate a user event
        $userData = ['id' => 1, 'name' => 'John Doe']; // Example user data
        $event = new UserCreated($userData);

        event($event);
        // Call the handle method of the listener manually
        $listener->handle($event);

        // Assert that the event listener has properly handled the event
        Event::assertDispatched(UserCreated::class, function ($event) use ($userData) {
            return $event->user === $userData;
        });

        // Assert that the File::append method was called with the correct arguments
        File::shouldHaveReceived('append')->once()->with(
            storage_path('logs/user_events.log'),
            json_encode($userData) . PHP_EOL
        );
    }
}
