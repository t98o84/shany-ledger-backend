<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('workspace_id');
            $table->boolean('exist')->nullable()->storedAs('case when deleted_at is null then 1 else null end');
            $table->string('role');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'workspace_id', 'exist']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('workspace_id')->references('id')->on('workspaces');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_accounts');
    }
};
