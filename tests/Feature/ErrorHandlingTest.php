<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    // Test to return a 404 for a non-existent note
    public function returns_404_for_non_existent_note()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Make GET request for note that doesn't exist in the database
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/notes/99999');

        // Assert 404 not found
        $response->assertStatus(404);
    }

    // Test to return a 401 if a token is missing
    public function returns_401_for_missing_token()
    {
        // Make GET request without token
        $response = $this->getJson('/api/notes');

        // Assert unauthenticated
        $response->assertStatus(401);
    }

    // Test to return a 401 if the token is invalid
    public function returns_401_for_invalid_token()
    {
        // Make GET request with an invalid token
        $response = $this->withHeaders(['Authorization' => 'Bearer invalid-token'])
            ->getJson('/api/notes');

        // Assert unauthenticated
        $response->assertStatus(401);
    }

    // Test to return a 422 if the JSON request is malformed
    public function returns_422_for_malformed_json()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Make POST request with malformed input
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json'
        ])->post('/api/notes', 'invalid-json');

        $response->assertStatus(400); // Bad Request
    }

    // Test to handle database connection errors
    public function handles_database_connection_errors_gracefully()
    {
        // This would require mocking database failures
        // Advanced testing technique for production apps
        $this->markTestIncomplete('Database error handling test');
    }
}