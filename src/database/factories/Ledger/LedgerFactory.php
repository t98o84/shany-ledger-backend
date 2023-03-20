<?php

namespace Database\Factories\Ledger;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\ValueObjects\Ledger\LedgerPublicStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ledger\Ledger>
 */
class LedgerFactory extends Factory
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
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->realText(),
            'public_status' => LedgerPublicStatus::WorkspaceParticipant->value,
            'created_by' => User::factory(),
        ];
    }
}
