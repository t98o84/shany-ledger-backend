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
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->boolean('valid_email')->nullable()->default(1)->comment('論理削除した場合にこのカラムをnullにし同一のメールアドレスで登録できるようにするためのカラム');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['email', 'valid_email']);
            $table->foreign('avatar_id')->references('id')->on('files')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
