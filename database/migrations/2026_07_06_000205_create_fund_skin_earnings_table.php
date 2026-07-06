<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_skin_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skin_id')->constrained('fund_skins')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('month');
            $table->decimal('revenue', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['skin_id', 'month']);
            $table->index(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_skin_earnings');
    }
};
