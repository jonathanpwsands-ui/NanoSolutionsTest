<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    // Test to register a new User successfully
    public function test_register_success()
    {
        // Data to define a new user
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Make POST request
        $response = $this->postJson('/api/register', $data);

        // Assert response status is 201 (created)
        $response->assertStatus(201);

        // Assert structure of the user
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            'token'
        ]);

        // Assert that user exists in the database
        $this->assertDatabaseHas('users', [
            'email' => $data['email']
        ]);

        // Assert that user's email matches the submitted email
        $response->assertJsonPath('user.email', $data['email']);

        // Assert that user's name matches the submitted name
        $response->assertJsonPath('user.name', $data['name']);
    }

    // Test to register a new User with an already registered email
    public function test_register_duplicate_email()
    {
        // Create a new User with the following email address
        User::factory()->create([
            'email' => 'test@example.com'
        ]);

        // Data to define a new user
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Make POST request
        $response = $this->postJson('/api/register', $data);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);
    }

    // Test to register a new User and fail
    public function test_register_fail()
    {
        // Make POST request with no data
        $response = $this->postJson('/api/register', []);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);

        // Assert validation errors
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    // Test to register a new User and fail password confirmation
    public function test_register_password_confirmation_fail()
    {
        // Data to define a new user
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ];

        // Make POST request
        $response = $this->postJson('/api/register', $data);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);

        // Assert validation errors
        $response->assertJsonValidationErrors('password');
    }

    // Test to login successfully
    public function test_login_success()
    {
        // Create a new User with password
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        // Make POST request with User email and password
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert user information and their token
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token'
        ]);
    }

    // Test to login using the wrong credentials
    public function test_login_wrong_credentials()
    {
        // Create a new User
        $user = User::factory()->create();

        // Make POST request with User email and wrong password
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        // Assert unauthorised
        $response->assertStatus(401);

        // Assert message confirming invalid credentials
        $response->assertJson(['message' => 'Invalid login credentials']);
    }

    // Test to login and fail validation
    public function test_login_fail()
    {
        // Make POST request with no data
        $response = $this->postJson('/api/login', []);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);

        // Assert validation errors
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    // Test to authorise the User successfully
    public function test_get_me_authenticated()
    {
        // Create a new User
        $user = User::factory()->create();

        // Create token for the User
        $token = $user->createToken('test')->plainTextToken;

        // Assert GET request with User's token
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->getJson('/api/me');

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert user information
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    // Test to authorise the User while unauthenticated
    public function test_get_me_unauthenticated()
    {
        // Make GET request
        $response = $this->getJson('/api/me');

        // Assert unauthorised
        $response->assertStatus(401);
    }

    // Test to logout the User successfully
    public function test_logout_success()
    {
        // Create a new User
        $user = User::factory()->create();

        // Create token for the User
        $token = $user->createToken('test')->plainTextToken;

        // Make POST request with User's token
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->postJson('/api/logout');

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert message confirming logout
        $response->assertJson(['message' => 'Logged out successfully']);
    }

    // Test to logout the User while unauthenticated
    public function test_logout_unauthenticated()
    {
        // Make POST request
        $response = $this->postJson('/api/logout');

        // Assert unauthorised
        $response->assertStatus(401);
    }
}