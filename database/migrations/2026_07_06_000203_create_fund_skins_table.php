<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_skins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('cost', 12, 2);
            $table->decimal('uu_price', 12, 2)->nullable();
            $table->decimal('uu_fee_rate', 5, 4)->nullable()->default(0.02);
            $table->decimal('buff_price', 12, 2)->nullable();
            $table->decimal('buff_fee_rate', 5, 4)->nullable()->default(0.025);
            $table->decimal('daily_rental', 8, 2)->nullable()->default(1.5);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_skins');
    }
};
