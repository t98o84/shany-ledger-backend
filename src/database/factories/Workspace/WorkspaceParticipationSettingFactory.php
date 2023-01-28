<?php

namespace Database\Factories\Workspace;

use App\Models\Workspace\WorkspaceParticipationSettingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace\WorkspaceParticipationSetting>
 */
class WorkspaceParticipationSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'method' => WorkspaceParticipationSettingMethod::cases()[array_rand(WorkspaceParticipationSettingMethod::cases())]->value
        ];
    }
}
