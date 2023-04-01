<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_aggregation_settings', function (Blueprint $table) {
            $table->foreignUuid('ledger_id')->primary()->constrained('ledgers')->cascadeOnUpdate()->cascadeOnDelete();

            $table->double('max_input')->nullable();
            $table->double('min_input')->nullable();

            $table->double('max_output')->nullable();
            $table->double('min_output')->nullable();

            $table->double('max_total')->nullable();
            $table->double('min_total')->nullable();

            $table->unsignedTinyInteger('fixed_point_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_aggregation_settings');
    }
};
