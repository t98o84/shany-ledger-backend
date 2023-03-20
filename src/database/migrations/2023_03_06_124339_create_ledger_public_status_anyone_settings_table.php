<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_public_status_anyone_settings', function (Blueprint $table) {
            $table->foreignUuid('ledger_id')->primary()->constrained('ledgers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('url')->unique();
            $table->boolean('allow_comments')->default(false);
            $table->boolean('allow_editing')->default(false);
            $table->boolean('allow_duplicate')->default(false);
            $table->timestamp('expiration_started_at')->nullable();
            $table->timestamp('expiration_ended_at')->nullable();
            $table->timestamps();
            $table->operators();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_public_status_anyone_settings');
    }
};
