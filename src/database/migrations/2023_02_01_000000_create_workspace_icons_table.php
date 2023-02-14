<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_icons', function (Blueprint $table) {
            $table->uuid('workspace_id')->primary();

            $table->foreign('workspace_id')->references('id')->on('workspaces');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_icons');
    }
};
