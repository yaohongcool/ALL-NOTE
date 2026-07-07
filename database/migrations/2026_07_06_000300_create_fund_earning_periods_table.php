<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_earning_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 20);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::table('fund_skin_earnings', function (Blueprint $table) {
            $table->foreignId('period_id')->nullable()->constrained('fund_earning_periods')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fund_skin_earnings', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropColumn('period_id');
        });

        Schema::dropIfExists('fund_earning_periods');
    }
};
