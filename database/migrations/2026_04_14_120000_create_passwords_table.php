<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passwords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('account', 150);
            $table->text('encrypted_password');
            $table->string('tags')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'name']);
            $table->index(['user_id', 'account']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passwords');
    }
};