<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('passwords', function (Blueprint $table) {
            $table->index(['user_id', 'updated_at'], 'passwords_user_updated_idx');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->index(['user_id', 'updated_at'], 'assets_user_updated_idx');
            $table->index(['user_id', 'due_date'], 'assets_user_due_idx');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index(['user_id', 'updated_at'], 'documents_user_updated_idx');
            $table->index(['user_id', 'due_date'], 'documents_user_due_idx');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'events_user_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('passwords', function (Blueprint $table) {
            $table->dropIndex('passwords_user_updated_idx');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('assets_user_updated_idx');
            $table->dropIndex('assets_user_due_idx');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_user_updated_idx');
            $table->dropIndex('documents_user_due_idx');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_user_created_idx');
        });
    }
};
