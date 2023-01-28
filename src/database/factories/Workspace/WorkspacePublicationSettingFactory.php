<?php

namespace Database\Factories\Workspace;

use App\Models\Workspace\WorkspaceParticipationSettingMethod;
use App\Models\Workspace\WorkspacePublicationSettingState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace\WorkspacePublicationSetting>
 */
class WorkspacePublicationSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'state' => WorkspacePublicationSettingState::cases()[array_rand(WorkspacePublicationSettingState::cases())]->value 
        ];
    }
}
