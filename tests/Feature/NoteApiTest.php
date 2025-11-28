<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Note;
use App\Models\User;

class NoteApiTest extends TestCase
{
    // Test to List All Notes
    public function list_notes()
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

    // Test to List All Notes within current scope
    public function list_notes_scoping()
    {
        // Create first user
        $user = User::factory()->create();

        // Create token for first user
        $token = $user->createToken('test')->plainTextToken;

        // Create second user
        $otherUser = User::factory()->create();

        // Create several notes for first user
        Note::factory()->count(3)->for($user)->create();

        // Create several notes for second user
        Note::factory()->count(2)->for($otherUser)->create();

        // Make GET request with first user's token
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->getJson('/api/notes');

        // Assert HTTP OK for the number of notes that the first user has
        $response->assertStatus(200)->assertJsonCount(3); 
    }

    // Test to List All Notes without authentication
    public function list_notes_unauthenticated()
    {
        // Make GET request
        $response = $this->getJson('/api/notes');

        // Return unauthenticated
        $response->assertStatus(401);
    }

    // Test to Create Note
    public function create_note_success()
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

    // Test to Create Note and fail validation
     public function create_note_fail()
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

    // Test to Create Note without authentication
    public function create_note_unauthenticated()
    {
        // Data to be posted
        $data = ['title' => 'Test', 'content' => 'Test content'];

        // Make POST request
        $response = $this->postJson('/api/notes', $data);

        // Assert unauthenticated
        $response->assertStatus(401);
    }

    // Test to Create Note without a title
    public function create_note_fails_with_empty_title()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Make POST request with no title
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => '',
                'content' => 'Valid content'
            ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    // Test to Create Note with a title that doesn't meet the minimum length
    public function create_note_fails_with_title_too_short()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Make POST request with a title shorter than minimum length
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => 'ab',
                'content' => 'Valid content'
            ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    // Test to Create Note with content that doesn't meet the minimum length
    public function create_note_fails_with_content_too_short()
    {
        // Create user
        $user = User::factory()->create();
        
        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Make POST request with content shorter than minimum length
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => 'Valid Title',
                'content' => 'ab'
            ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors('content');
    }

    // Test to Create Note with a title that contains special characters
    public function create_note_fails_with_special_characters_in_title()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Make POST request with title containing special characters
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => '<script>alert("xss")</script>',
                'content' => 'Valid content'
            ]);

        // Should either sanitize or reject
        $response->assertStatus(201);
        $note = Note::latest()->first();
        $this->assertStringNotContainsString('<script>', $note->title);
    }

    // Test to Retrieve Note
    public function retrieve_note()
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

    // // Test to Retrieve Note without authorisation
    public function retrieve_note_unauthorized()
    {
        // Create first user
        $userA = User::factory()->create();

        // Create token for first user
        $tokenA = $userA->createToken('test')->plainTextToken;

        // Create second user
        $userB = User::factory()->create();

        // Create note for second user
        $noteB = Note::factory()->for($userB)->create();

        // Make GET request to the API route for the second user's note using the first user's token
        $response = $this->withHeaders(['Authorization' => "Bearer {$tokenA}"])->getJson("/api/notes/{$noteB->id}");

        // Assert unauthorised
        $response->assertStatus(403);
    }

    // Test to Update a Note
    public function update_note()
    {
        // Create user
        $user = User::factory()->create();
        
        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Create note for the user in the database
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

    // Test to Update a Note and fail
    public function update_note_fail()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for the user
        $token = $user->createToken('test')->plainTextToken;

        // Create note for the user in the database
        $note = Note::factory()->for($user)->create();

        // Data to update with
        $data = ['title' => 'a', 'content' => 'a'];

        // Make PUT request to update the note
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])->putJson("/api/notes/{$note->id}", $data);

        // Assert response status is 422 (validation failed)
        $response->assertStatus(422);
    }

    // Test to Update a Note without authentication
    public function update_note_unauthenticated()
    {
        // Data to update with
        $updateData = ['title' => 'Updated Title', 'content' => 'Updated content'];

        // Make PUT request WITHOUT auth header
        $response = $this->putJson("/api/notes/999", $updateData);

        // Assert unauthenticated
        $response->assertStatus(401);
    }

    // Test to Update a Note without authorisation
    public function update_note_unauthorized()
    {
        // Create the first user
        $userA = User::factory()->create();

        // Create token for the first user
        $tokenA = $userA->createToken('test')->plainTextToken;

        // Create the second user
        $userB = User::factory()->create();

        // Create a note for the second user
        $noteB = Note::factory()->for($userB)->create();

        // Data to update with
        $updateData = ['title' => 'Hack', 'content' => 'Hack'];

        // Make PUT request to second user's note using first user's token
        $response = $this->withHeaders(['Authorization' => "Bearer {$tokenA}"])->putJson("/api/notes/{$noteB->id}", $updateData);

        // Assert unauthorised
        $response->assertStatus(403);
    }

    // Test to Update a Note with invalid data
    public function update_note_fails_with_invalid_data()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Create note for user
        $note = Note::factory()->for($user)->create();

        // Make PUT request with invalid data
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->putJson("/api/notes/{$note->id}", [
                'title' => 'ab',
                'content' => 'ab'
            ]);

        // Assert validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    }

    // Test to Delete a Note
    public function delete_note()
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

        // Assert message confirming note deletion
        $response->assertJson(['message' => 'Note deleted successfully']);
    }

    // Test to Delete a Note without authentication
    public function delete_note_unauthenticated()
    {
        // Send DELETE request to the API route
        $response = $this->deleteJson('/api/notes/999');

        // Assert unauthorised
        $response->assertStatus(401);
    }

    // Test to Delete a Note without authorisation
    public function delete_note_unauthorized()
    {
        // Create the first user
        $userA = User::factory()->create();

        // Create token for the first user
        $tokenA = $userA->createToken('test')->plainTextToken;

        // Create the second user
        $userB = User::factory()->create();

        // Create a note for the second user
        $noteB = Note::factory()->for($userB)->create();

        // Send DELETE request to the API route for the second user's note using the first user's token
        $response = $this->withHeaders(['Authorization' => "Bearer {$tokenA}"])->deleteJson("/api/notes/{$noteB->id}");

        // Assert unauthorised
        $response->assertStatus(403);
    }

    // Test to return all of a user's notes in the note list
    public function notes_list_returns_all_user_notes()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Create 25 notes
        Note::factory()->count(25)->for($user)->create();

        // Make GET request
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/notes');

        // Assert that user has 25 notes
        $response->assertStatus(200)
            ->assertJsonCount(25);
    }

    // Test to check if the note list can display no notes
    public function notes_list_handles_empty_results()
    {
        // Create user
        $user = User::factory()->create();

        // Create token for user
        $token = $user->createToken('test')->plainTextToken;

        // Make GET request
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/notes');

        // Assert that user has no notes
        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    // Test to check if note response give the correct structure
    public function note_response_has_correct_structure()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $note = Note::factory()->for($user)->create();

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'id' => $note->id,
                'title' => $note->title
            ]);
    }

    // Test to check if error response has the correct structure
    public function error_response_has_correct_structure()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password'
                ]
            ]);
    }
}