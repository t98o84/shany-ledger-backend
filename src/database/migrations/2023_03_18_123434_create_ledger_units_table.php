<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_units', function (Blueprint $table) {
            $table->foreignUuid('ledger_id')->primary()->constrained('ledgers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('symbol');
            $table->string('display_position');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_units');
    }
};
