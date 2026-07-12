<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('master_password_hash')->nullable()->after('password');
            $table->timestamp('master_password_set_at')->nullable()->after('master_password_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['master_password_hash', 'master_password_set_at']);
        });
    }
};
