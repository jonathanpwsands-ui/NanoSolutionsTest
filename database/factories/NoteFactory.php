<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
        ];
    }

    public function withLongContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->paragraphs(20, true),
        ]);
    }

    public function withShortContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->sentence(),
        ]);
    }

    public function withSpecialCharacters(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => '<script>alert("xss")</script>',
            'content' => 'Content with <b>HTML</b> tags',
        ]);
    }
}
