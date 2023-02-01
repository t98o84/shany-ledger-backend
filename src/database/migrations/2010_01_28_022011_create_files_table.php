<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('fileable');
            $table->string('disk');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('size');
            $table->string('mime_type');
            $table->string('visibility');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};

// Base model factory
App\Models\Image::factory()->define(function (Faker $faker) {
    return [
        'url' => $faker->imageUrl(),
        'imageable_id' => function () {
            return App\Models\Post::factory()->create()->id;
        },
        'imageable_type' => App\Models\Post::class,
    ];
});

// Derived model factory
App\Models\Image::factory()->state('avatar', function (Faker $faker) {
    return [
        'imageable_id' => function () {
            return App\Models\User::factory()->create()->id;
        },
        'imageable_type' => App\Models\User::class,
    ];
});
