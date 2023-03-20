<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained('workspaces')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('public_status');
            $table->timestamps();
            $table->softDeletes();
            $table->operators();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
