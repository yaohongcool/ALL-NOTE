<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_monthlies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('month');
            $table->decimal('income', 12, 2)->default(0);
            $table->decimal('expense', 12, 2)->default(0);
            $table->decimal('savings_target', 12, 2)->nullable()->default(0);
            $table->decimal('savings_actual', 12, 2)->nullable()->default(0);
            $table->string('savings_status', 15)->nullable()->default('uncompleted');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month']);
            $table->index(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_monthlies');
    }
};
