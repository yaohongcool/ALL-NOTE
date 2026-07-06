<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skin_id')->constrained('fund_skins')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->decimal('rate', 8, 2)->default(3.8);
            $table->decimal('discount', 3, 2)->default(0.8);
            $table->integer('lease_days');
            $table->integer('offhand_days')->default(8);
            $table->decimal('fee_rate', 4, 2)->default(0.99);
            $table->decimal('revenue', 12, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('skin_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_rentals');
    }
};
