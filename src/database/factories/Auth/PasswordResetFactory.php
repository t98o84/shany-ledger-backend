<?php

namespace Database\Factories\Auth;

use App\Models\Auth\PasswordReset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auth\PasswordReset>
 */
class PasswordResetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'token' => PasswordReset::hashToken(PasswordReset::createToken()),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => (string) now()->subMinutes(PasswordReset::minutesToExpiration() + 1),
        ]);
    }
}
