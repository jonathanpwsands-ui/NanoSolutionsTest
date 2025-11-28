<?php

namespace Tests\Helpers;

use App\Models\User;

trait TestHelpers
{
    protected function createAuthenticatedUser(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token,
            'headers' => ['Authorization' => "Bearer {$token}"]
        ];
    }

    protected function actingAsUser(User $user = null): array
    {
        $user = $user ?? User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token,
            'headers' => ['Authorization' => "Bearer {$token}"]
        ];
    }
}