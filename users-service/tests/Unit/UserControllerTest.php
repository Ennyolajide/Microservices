<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use App\Services\AmqpService;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_with_valid_data()
    {
        // Mock AmqpService to prevent actual publishing
        $amqpMock = Mockery::mock(AmqpService::class);
        $amqpMock->shouldReceive('getConnection')->andReturnSelf();
        $this->app->instance(AmqpService::class, $amqpMock);


        // Create a fake user using Laravel's factory
        $user = User::factory()->make();
        $userData = $user->toArray(); // Convert to array

        $response = $this->postJson('/api/users', $userData);


        // dd($response);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'User created']);

        // Assert User model creation
        $this->assertDatabaseHas('users', $userData);
    }

    public function test_store_with_invalid_data()
    {
        $userData = [
            'email' => 'invalid_email', // Invalid email format
            'firstName' => 'John',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors']);
    }
}
