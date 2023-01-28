<?php

namespace Database\Factories\Workspace;

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
            'url' => \Str::random(fake()->numberBetween(3, 16)),
            'name' => fake()->name(),
            'icon_id' => null,
            'description' => fake()->realText(50)
        ];
    }

}
