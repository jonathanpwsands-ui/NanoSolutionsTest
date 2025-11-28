<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    // Test for note list performance with many notes
    public function notes_list_performs_well_with_many_notes()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        // Create 100 notes
        Note::factory()->count(100)->for($user)->create();

        $startTime = microtime(true);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/notes');

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to ms

        $response->assertStatus(200);
        
        // Assert response time is under 500ms
        $this->assertLessThan(500, $executionTime, 
            "Notes list took {$executionTime}ms, should be under 500ms");
    }

    // Test for database query optimisation
    public function database_queries_are_optimized()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        Note::factory()->count(10)->for($user)->create();

        // Enable query logging
        \DB::enableQueryLog();

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/notes');

        $queries = \DB::getQueryLog();

        // Should not have N+1 query problem
        $this->assertLessThanOrEqual(3, count($queries), 
            'Too many database queries executed');
    }
}