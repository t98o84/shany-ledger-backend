<?php

namespace Database\Factories\Shared;

use App\Models\Shared\FileVisibility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shared\File>
 */
class FileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => (string) \Str::orderedUuid(),
            'disk' => 'local',
            'path' => preg_replace('/^\//', '', fake()->filePath()) . '.' . fake()->fileExtension(),
            'original_name' => basename(fake()->filePath() . '.' . fake()->fileExtension()),
            'size' => fake()->randomNumber(),
            'mime_type' => fake()->mimeType(),
            'visibility' => FileVisibility::Private,
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'visibility' => FileVisibility::Public,
        ]);
    }
}
