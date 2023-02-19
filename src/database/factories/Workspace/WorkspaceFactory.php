<?php

namespace Database\Factories\Workspace;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace\Workspace>
 */
class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Str::orderedUuid(),
            'owner_id' => User::factory(),
            'url' => \Str::random(fake()->numberBetween(3, 16)),
            'name' => fake()->name(),
            'description' => fake()->realText(50),
            'is_public' => false,
        ];
    }

    public function published(): WorkspaceFactory
    {
        return $this->state(fn(array $attributes) => [
            'is_public' => true,
        ]);
    }
}
