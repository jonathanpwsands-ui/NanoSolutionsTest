<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    // Test to List All Notes
    public function test_list_notes()
    {
        // Create several notes in the database
        Note::factory()->count(5)->create();

        // Make GET request
        $response = $this->get('/api/notes');

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert returned values of each note
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'contents', 'timestamps']
        ]);

        // Assert the number of notes in the response
        $response->assertJsonCount(5);
    }

    // Test to Create Note
    public function test_create_note()
    {
         // Data to be posted
        $data = [
            'title' => 'New Note',
            'content' => 'Note content here'
        ];

        // Make POST request
        $response = $this->postJson('/api/notes', $data);

        // Assert response status is 201 (created)
        $response->assertStatus(201);

        // Assert structure of the note
        $response->assertJsonStructure([
            'id',
            'title',
            'content',
            'timestamps',
        ]);

        // Assert that note is in the database
        $this->assertDatabaseHas('notes', [
            'title' => 'New Note',
            'content' => 'Note content here',
        ]);
    }

    // Test to Retrieve Note
    public function test_retrieve_note()
    {
        // Create note in the database
        $post = Post::create([
            'title' => 'Note Title',
            'content'  => 'Note content here',
        ]);

        // Make GET request to the API route
        $response = $this->getJson("/api/notes/{$note->id}");

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert returned values that match the note
        $response->assertJson([
            'id'    => $note->id,
            'title' => $note->title,
            'content'  => $note->content,
            'timestamps' => $note->timestamps,
        ]);

        // Delete note after test completion
        $note->delete();
    }

    // Test to Update a Note
    public function test_update_note()
    {
        // Create note in the database
        $post = Post::create([
            'title' => 'Original Title',
            'content'  => 'Original Content',
        ]);

        // Data to update with
        $updateData = [
            'title' => 'Updated Title',
            'content'  => 'Updated Content',
        ];

        try {
            // Make PUT request to update the note
            $response = $this->patchJson("/api/notes/{$note->id}", $updateData);

            // Assert HTTP OK
            $response->assertStatus(200);

            // Assert returned updated values
            $response->assertJson($updateData);

            // Assert changes in the database
            $this->assertDatabaseHas('notes', [
                'id'    => $note->id,
                'title' => 'Updated Title',
                'content'  => 'Updated Content',
            ]);

        } finally {
            // Delete note after test completion
            $note->delete();
        }
    }

    // Test to Delete a Note
    public function test_delete_note()
    {
        // Create note in the database
        $note = Note::create([
            'title' => 'Note Title',
            'content'  => 'Note content here',
        ]);

        // Send DELETE request to the API route
        $response = $this->deleteJson("/api/posts/{$note->id}");

        // Assert HTTP OK
        $response->assertStatus(200);

        // Assert that note is not in the database
        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }
}
