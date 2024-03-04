<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Services\AmqpService;

class UserControllerIntegrationTest extends TestCase
{
    private $testChannel;
    private $testConnection;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup test AMQP connection
        $this->testConnection = AmqpService::getConnection();
        $this->testChannel = $this->testConnection->channel();
        $this->testChannel->queue_declare('user_events', false, true, false, false);

        // Purge the queue to ensure it's empty before each test
        $this->testChannel->queue_purge('user_events');
    }

    protected function tearDown(): void
    {
        $this->testChannel->close();
        $this->testConnection->close();
        parent::tearDown();
    }

    public function test_store_and_publish_event()
    {
        $data = User::factory()->make()->toArray();

        // Trigger the API call
        $response = $this->postJson('/api/users', $data);
        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'User created']);

        // Consume the message
        $callback = function ($message) use ($data) {
            $payload = json_decode($message->body, true);

            // Assertions
            $this->assertEquals($data['email'], $payload['email']);
            $this->assertEquals($data['firstName'], $payload['firstName']);
            $this->assertEquals($data['lastName'], $payload['lastName']);

            $message->ack();
        };

        $this->testChannel->basic_consume('user_events', '', false, false, false, false, $callback);

        // Process messages (adjust timeout as needed)
        $this->testChannel->wait(null, false, 5); // 5 second timeout
    }
}
