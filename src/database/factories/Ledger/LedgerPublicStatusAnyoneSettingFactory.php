<?php

namespace Database\Factories\Ledger;

use App\Models\Ledger\Ledger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ledger\LedgerPublicStatusAnyoneSetting>
 */
class LedgerPublicStatusAnyoneSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ledger_id' => Ledger::factory(),
            'url' => $this->faker->uuid(),
            'allow_comments' => false,
            'allow_editing' => false,
            'allow_duplicate' => false,
            'expiration_started_at' => null,
            'expiration_ended_at' => null,
        ];
    }

    public function allowComments(): self
    {
        return $this->state(fn(array $attributes) => [
            'allow_comments' => true,
        ]);
    }

    public function allowEditing(): self
    {
        return $this->state(fn(array $attributes) => [
            'allow_editing' => true,
        ]);
    }

    public function allowDuplicate(): self
    {
        return $this->state(fn(array $attributes) => [
            'allow_duplicate' => true,
        ]);
    }

    public function expired(): self
    {
        return $this->state(fn(array $attributes) => [
            'expired_at' => $this->faker->dateTime(),
        ]);
    }
}
