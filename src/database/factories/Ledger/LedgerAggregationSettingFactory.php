<?php

namespace Database\Factories\Ledger;

use App\Models\Ledger\Ledger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ledger\LedgerAggregationSetting>
 */
class LedgerAggregationSettingFactory extends Factory
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
            'max_input' => 100,
            'min_input' => -100,
            'max_output' => 100,
            'min_output' => -100,
            'max_total' => 1000,
            'min_total' => -1000,
            'fixed_point_number' => 5,
        ];
    }
}
