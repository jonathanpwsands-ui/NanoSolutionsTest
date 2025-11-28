<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    // Test to prevent malicious SQL injection attempts
    public function sql_injection_attempts_are_prevented()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => "'; DROP TABLE notes; --",
                'content' => 'Test content'
            ]);

        // Should either reject or sanitize
        $response->assertStatus(201);
        
        // Verify table still exists
        $this->assertDatabaseCount('notes', 1);
    }

    // Test to sanitise XSS attempts
    public function xss_attempts_are_sanitized()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => '<script>alert("XSS")</script>',
                'content' => '<img src=x onerror=alert("XSS")>'
            ]);

        $response->assertStatus(201);
        
        $note = Note::latest()->first();
        $this->assertStringNotContainsString('<script>', $note->title);
        $this->assertStringNotContainsString('onerror', $note->content);
    }

    // Test to prevent vulnerabilities through mass assignment
    public function mass_assignment_vulnerabilities_are_prevented()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/notes', [
                'title' => 'Test Note',
                'content' => 'Test content',
                'user_id' => 999, // Attempt to set different user_id
                'id' => 1000, // Attempt to set ID
            ]);

        $response->assertStatus(201);
        
        $note = Note::latest()->first();
        $this->assertEquals($user->id, $note->user_id);
        $this->assertNotEquals(1000, $note->id);
    }

    // Test to prevent brute force logins via rate limiting
    public function rate_limiting_prevents_brute_force()
    {
        // Make 6 login attempts (assuming limit is 5)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // 6th attempt should be rate limited
        $response->assertStatus(429); // Too Many Requests
    }
}