<?php

namespace Database\Factories\Ledger;

use App\Models\Ledger\Ledger;
use App\ValueObjects\Ledger\LedgerUnitDisplayPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ledger\LedgerUnit>
 */
class LedgerUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ledger_id' => Ledger::factory(),
            'symbol' => $this->faker->word(),
            'display_position' => \Arr::random(LedgerUnitDisplayPosition::cases())->value,
            'description' => $this->faker->realText(),
        ];
    }
}
