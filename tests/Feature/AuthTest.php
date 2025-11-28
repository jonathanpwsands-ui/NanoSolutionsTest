<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // Test to register a new User successfully
    public function register_success()
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
    public function register_duplicate_email()
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
    public function register_fail()
    {
        // Make POST request with no data
        $response = $this->postJson('/api/register', []);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);

        // Assert validation errors
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    // Test to register a new User and fail password confirmation
    public function register_password_confirmation_fail()
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

    // Test to fail registration with an invalid email format
    public function register_fails_with_invalid_email_format()
    {
        // Data to define a new user
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    // Test to fail registration with a password shorter than the minimum length
    public function register_fails_with_short_password()
    {
        // Data to define a user with
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    // Test to fail registration without a name
    public function register_fails_with_missing_name()
    {
        // Data to define a user with
        $response = $this->postJson('/api/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    // Test to login successfully
    public function login_success()
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
    public function login_wrong_credentials()
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
    public function login_fail()
    {
        // Make POST request with no data
        $response = $this->postJson('/api/login', []);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);

        // Assert validation errors
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    // Test to fail login with no credentials
    public function login_fails_with_empty_credentials()
    {
        // Make POST request with empty attributes
        $response = $this->postJson('/api/login', [
            'email' => '',
            'password' => '',
        ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    // Test to fail login with a user that doesn't exist in the database
    public function login_fails_with_non_existent_user()
    {
        // Make POST request with nonexistent credentials
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        // Assert unauthorised
        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid login credentials']);
    }

    // Test to authorise the User successfully
    public function get_me_authenticated()
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
    public function get_me_unauthenticated()
    {
        // Make GET request
        $response = $this->getJson('/api/me');

        // Assert unauthorised
        $response->assertStatus(401);
    }

    // Test to logout the User successfully
    public function logout_success()
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
    public function logout_unauthenticated()
    {
        // Make POST request
        $response = $this->postJson('/api/logout');

        // Assert unauthorised
        $response->assertStatus(401);
    }
}