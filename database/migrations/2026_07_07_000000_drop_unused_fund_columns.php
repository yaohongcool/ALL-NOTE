<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fund_accounts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'sort']);
            $table->dropColumn('sort');
        });

        Schema::table('fund_monthlies', function (Blueprint $table) {
            $table->dropColumn(['expense', 'savings_target', 'savings_actual', 'savings_status']);
        });
    }

    public function down(): void
    {
        Schema::table('fund_monthlies', function (Blueprint $table) {
            $table->decimal('expense', 12, 2)->default(0);
            $table->decimal('savings_target', 12, 2)->nullable();
            $table->decimal('savings_actual', 12, 2)->nullable();
            $table->string('savings_status', 15)->nullable()->default('uncompleted');
        });

        Schema::table('fund_accounts', function (Blueprint $table) {
            $table->integer('sort')->default(0);
        });
    }
};
