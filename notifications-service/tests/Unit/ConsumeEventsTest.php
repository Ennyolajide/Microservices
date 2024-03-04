<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Events\UserCreated;
use App\Services\AmqpService;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ConsumeUserEvents;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsumeEventsTest extends TestCase
{

    public function test_handle_user_event_dispatches_event_and_acknowledges_message()
    {
        // Mock the AMQPStreamConnection to avoid actual connection attempts
        $this->mock(AmqpService::class, function ($mock) {
            $mock->shouldReceive('getConnection')->andReturnSelf();
        });

        // Mock the UserCreated event listener to avoid actual file operations
        Event::fake();

        // Create an instance of the ConsumeUserEvents command
        $command = new ConsumeUserEvents();

        // Set up a buffered output to capture command output
        $output = new BufferedOutput;

        // Simulate message data
        $messageData = [
            'lastName' => fake()->name(),
            'email' => fake()->email(),
            'firstName' => fake()->name(),
        ];

        // Create an AMQPMessage instance and set the message data
        $message = new AMQPMessage(json_encode($messageData));
        $command->messageData = $messageData;

        // Execute the handleUserEvent method with buffered output
        $command->handleUserEvent($output);

        // Assert that the UserCreated event was dispatched with the correct data
        Event::assertDispatched(UserCreated::class, function ($event) use ($messageData) {
            return $event->user === $messageData;
        });

        // Assert that acknowledgment message is captured in the buffered output
        $this->assertStringContainsString(" [x] Acknowledging message...", $output->fetch());
    }
}
