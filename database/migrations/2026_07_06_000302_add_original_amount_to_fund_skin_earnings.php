<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fund_skin_earnings', function (Blueprint $table) {
            $table->decimal('original_amount', 12, 2)->nullable()->after('revenue');
        });
    }

    public function down(): void
    {
        Schema::table('fund_skin_earnings', function (Blueprint $table) {
            $table->dropColumn('original_amount');
        });
    }
};
