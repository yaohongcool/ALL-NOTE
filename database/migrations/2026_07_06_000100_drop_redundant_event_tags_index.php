<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_tags', function (Blueprint $table) {
            $table->dropIndex('event_tags_user_id_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('event_tags', function (Blueprint $table) {
            $table->index(['user_id', 'name'], 'event_tags_user_id_name_index');
        });
    }
};
