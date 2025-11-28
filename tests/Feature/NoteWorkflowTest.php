<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    // Test to check if a user can complete a full note lifecycle
    public function user_can_complete_full_note_lifecycle()
    {
        // Register
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registerResponse->assertStatus(201);
        $token = $registerResponse->json('token');

        // Create note
        $createResponse = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => 'My First Note',
                'content' => 'This is my first note content'
            ]);

        $createResponse->assertStatus(201);
        $noteId = $createResponse->json('id');

        // Read note
        $readResponse = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson("/api/notes/{$noteId}");

        $readResponse->assertStatus(200)
            ->assertJson([
                'title' => 'My First Note',
                'content' => 'This is my first note content'
            ]);

        // Update note
        $updateResponse = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->putJson("/api/notes/{$noteId}", [
                'title' => 'Updated Note Title',
                'content' => 'Updated note content'
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Note Title',
                'content' => 'Updated note content'
            ]);

        // List notes
        $listResponse = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/notes');

        $listResponse->assertStatus(200)
            ->assertJsonCount(1);

        // Delete note
        $deleteResponse = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->deleteJson("/api/notes/{$noteId}");

        $deleteResponse->assertStatus(200);

        // Verify deletion
        $this->assertDatabaseMissing('notes', ['id' => $noteId]);
    }

    // Test to check if multiple users have their own notes isolated from each other
    public function multiple_users_have_isolated_notes()
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $token1 = $user1->createToken('test')->plainTextToken;
        $token2 = $user2->createToken('test')->plainTextToken;

        // User 1 creates notes
        $this->withHeaders(['Authorization' => "Bearer {$token1}"])
            ->postJson('/api/notes', [
                'title' => 'User 1 Note',
                'content' => 'Content for user 1'
            ]);

        // User 2 creates notes
        $this->withHeaders(['Authorization' => "Bearer {$token2}"])
            ->postJson('/api/notes', [
                'title' => 'User 2 Note',
                'content' => 'Content for user 2'
            ]);

        // User 1 should only see their note
        $response1 = $this->withHeaders(['Authorization' => "Bearer {$token1}"])
            ->getJson('/api/notes');

        $response1->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'User 1 Note']);

        // User 2 should only see their note
        $response2 = $this->withHeaders(['Authorization' => "Bearer {$token2}"])
            ->getJson('/api/notes');

        $response2->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'User 2 Note']);
    }

    // Test to check if a token can be invalidated by logout
    public function logout_invalidates_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Verify token works
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/me');
        $response->assertStatus(200);

        // Logout
        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/logout');

        // Token should no longer work
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/me');
        $response->assertStatus(401);
    }
}