<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

  
    public function user_can_register_successfully()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User created successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function user_cannot_register_with_existing_email()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Test User',
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422) // Unprocessable Entity
                 ->assertJsonValidationErrors(['email']);
    }


    public function user_can_login_with_correct_credentials()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user',
                     'token',
                 ]);
    }


    public function user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401) // Unauthorized
                 ->assertJson([
                     'message' => 'Invalid credentials',
                 ]);
    }


    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User logged out successfully',
                 ]);
    }
}
