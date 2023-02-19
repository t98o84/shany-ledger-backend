<?php

namespace Database\Factories\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccountRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace\Workspace>
 */
class WorkspaceAccountFactory extends Factory
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
            'user_id' => User::factory(),
            'workspace_id' => Workspace::factory(),
            'role' => WorkspaceAccountRole::Administrator->value,
        ];
    }

    public function published(): WorkspaceAccountFactory
    {
        return $this->state(fn(array $attributes) => [
            'is_public' => true,
        ]);
    }
}
