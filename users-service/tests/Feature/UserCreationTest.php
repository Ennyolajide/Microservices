<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;



class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCreationFlow()
    {
        // Write test to simulate end-to-end user creation flow


        // Generate fake user data using the model factory
        $userData = User::factory()->make()->toArray();

        // Send a POST request to the user creation endpoint with the generated user data
        $response = $this->postJson('/api/users', $userData);

        // Assert that the response status code is 201 (created)
        $response->assertStatus(201);

        // Assert that the response JSON contains the expected message
        $response->assertJson([
            'message' => 'User created'
        ]);

        // Assert that the user exists in the database
        $this->assertDatabaseHas('users', $userData);
    }
}
