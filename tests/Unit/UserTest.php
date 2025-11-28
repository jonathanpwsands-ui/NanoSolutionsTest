<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // Test to check if a user can have multiple notes
    public function user_can_have_multiple_notes()
    {
        // Create a new user
        $user = User::factory()->create();

        // Create 3 new notes for the user
        $notes = Note::factory()->count(3)->for($user)->create();

        // Assert the user has 3 notes
        $this->assertCount(3, $user->notes);
        $this->assertInstanceOf(Note::class, $user->notes->first());
    }

    // Test to check if the user's email is unique
    public function user_email_is_unique()
    {
        // Create a new user with an email
        $user = User::factory()->create(['email' => 'test@example.com']);
        
        // Expect a duplicate email to be created in the database
        $this->expectException(\Illuminate\Database\QueryException::class);
        // Create a new user with the same email
        User::factory()->create(['email' => 'test@example.com']);
    }

    // Test to check if the user's email has been hashed
    public function user_password_is_hashed()
    {
        // Create a new user with a password
        $user = User::factory()->create(['password' => 'password123']);
        
        // Assert the user's password
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(\Hash::check('password123', $user->password));
    }

    // Test to check if the user has fillable attributes
    public function user_has_fillable_attributes()
    {
        // Create a new user
        $user = new User();
        
        // Assert the user's attributes
        $this->assertEquals(['name', 'email', 'password'], $user->getFillable());
    }

    // Test to check if the user has hidden attributes
    public function user_has_hidden_attributes()
    {
        // Create a new user
        $user = User::factory()->create();
        
        // Cast user to an array
        $array = $user->toArray();
        
        // Assert if password and remember token are hidden
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }
}