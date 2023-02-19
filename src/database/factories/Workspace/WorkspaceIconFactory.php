<?php

namespace Database\Factories\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAvatar>
 */
class WorkspaceIconFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
        ];
    }
}
