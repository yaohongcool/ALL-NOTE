<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('passwords', function (Blueprint $table) {
            if (! Schema::hasColumn('passwords', 'phone')) {
                $table->string('phone', 50)->nullable()->after('encrypted_password');
            }

            if (! Schema::hasColumn('passwords', 'email')) {
                $table->string('email', 150)->nullable()->after('phone');
            }

            if (Schema::hasColumn('passwords', 'tags')) {
                $table->dropColumn('tags');
            }
        });
    }

    public function down(): void
    {
        Schema::table('passwords', function (Blueprint $table) {
            if (! Schema::hasColumn('passwords', 'tags')) {
                $table->string('tags')->nullable()->after('encrypted_password');
            }

            if (Schema::hasColumn('passwords', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('passwords', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};