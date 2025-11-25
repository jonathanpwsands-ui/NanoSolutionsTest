<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Note;
use App\Models\User;

class NoteApiTest extends TestCase
{
    // Test to List All Notes
    public function test_list_notes()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Create several notes for the user in the database
        Note::factory()->count(5)->for($user)->create();

        // Make GET request
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->getJson('/api/notes');

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert returned values of each note
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'content', 'created_at', 'updated_at',]
        ]);

        // Assert the number of notes in the response
        $response->assertJsonCount(5);
    }

    // Test to Create Note
    public function test_create_note_success()
    {
        // Create user
        $user = User::factory()->create();
        
        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

         // Data to be posted
        $data = [
            'title' => 'New Note',
            'content' => 'Note content here'
        ];

        // Make POST request
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->postJson('/api/notes', $data);

        // Assert response status is 201 (created)
        $response->assertStatus(201);

        // Assert structure of the note
        $response->assertJsonStructure([
            'id',
            'title',
            'content',
            'created_at',
            'updated_at',
        ]);

        // Assert that user's note is in the database
        $this->assertDatabaseHas('notes', [
            'title' => 'New Note',
            'content' => 'Note content here',
            'user_id' => $user->id,
        ]);
    }

     public function test_create_note_fail()
    {
        // Create user
        $user = User::factory()->create();
        
        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

         // Data to be posted
        $data = [
            'title' => 'a',
            'content' => 'a'
        ];

        // Make POST request
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->postJson('/api/notes', $data);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);
    }

    // Test to Retrieve Note
    public function test_retrieve_note()
    {
        // Create user
        $user = User::factory()->create();
        
        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Create user's note in the database
        $note = Note::factory()->for($user)->create([
            'title' => 'Note Title',
            'content'  => 'Note content here',
        ]);

        // Make GET request to the API route
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->getJson("/api/notes/{$note->id}");

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert returned values that match the note
        $response->assertJson([
            'id'    => $note->id,
            'title' => $note->title,
            'content'  => $note->content,
        ]);
    }

    // Test to Update a Note
    public function test_update_note()
    {
        // Create user
        $user = User::factory()->create();
        
        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Create note in the database
        $note = Note::factory()->for($user)->create([
            'title' => 'Original Title',
            'content'  => 'Original Content',
        ]);

        // Data to update with
        $updateData = [
            'title' => 'Updated Title',
            'content'  => 'Updated Content',
        ];

        // Make PUT request to update the note
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->putJson("/api/notes/{$note->id}", $updateData);

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert structure for updated values
        $response->assertJsonStructure([
            'id',
            'title',
            'content',
            'created_at',
            'updated_at',
        ]);

        // Assert returned updated values
        $response->assertJson([
            'title' => $updateData['title'],
            'content' => $updateData['content'],
        ]);


        // Assert changes in the database
        $this->assertDatabaseHas('notes', [
            'id'    => $note->id,
            'title' => 'Updated Title',
            'content'  => 'Updated Content',
        ]);
    }

    // Test to Delete a Note
    public function test_delete_note()
    {
        // Create user
        $user = User::factory()->create();
        
        // Create token for user
        $token = $user->createToken('test')->plainTextToken;
        
        // Create note in the database
        $note = Note::factory()->for($user)->create([
            'title' => 'Note Title',
            'content'  => 'Note content here',
        ]);

        // Send DELETE request to the API route
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->deleteJson("/api/notes/{$note->id}");

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert that note is not in the database
        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }
}