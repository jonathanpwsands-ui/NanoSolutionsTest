<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    // Test to check if a note belongs to a user
    public function note_belongs_to_user()
    {
        // Create a new user
        $user = User::factory()->create();

        // Create a new note
        $note = Note::factory()->for($user)->create();

        // Assert that the note belongs to the user
        $this->assertInstanceOf(User::class, $note->user);
        $this->assertEquals($user->id, $note->user->id);
    }

    // Test to check if a note has the required attributes
    public function note_has_required_attributes()
    {
        // Create a new note with filled attributes
        $note = Note::factory()->create([
            'title' => 'Test Title',
            'content' => 'Test Content'
        ]);

        // Assert the note's attributes
        $this->assertEquals('Test Title', $note->title);
        $this->assertEquals('Test Content', $note->content);
        $this->assertNotNull($note->user_id);
    }

    // Test to check if a note has timestamps (created_at, updated_at)
    public function note_has_timestamps()
    {
        // Create a new note
        $note = Note::factory()->create();

        // Assert the note's timestamps
        $this->assertNotNull($note->created_at);
        $this->assertNotNull($note->updated_at);
    }

    // Test to check if a note can be updated
    public function note_can_be_updated()
    {
        // Create a new note with a title
        $note = Note::factory()->create(['title' => 'Original']);
        
        // Update the note's title
        $note->update(['title' => 'Updated']);
        
        // Assert the note's new title
        $this->assertEquals('Updated', $note->fresh()->title);
    }
}