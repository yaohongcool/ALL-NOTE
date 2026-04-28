<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('status', 20)->default('已处理');
            $table->string('subject', 255)->nullable();
            $table->date('occurred_on')->nullable();
            $table->string('visibility', 20)->default('private');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'visibility']);
            $table->index(['user_id', 'occurred_on']);
            $table->index(['user_id', 'title']);
            $table->index(['user_id', 'subject']);
        });

        Schema::create('event_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('process')->nullable();
            $table->longText('result')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('event_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
            $table->index(['user_id', 'name']);
        });

        Schema::create('event_tag_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_tag_id')->constrained('event_tags')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'event_tag_id']);
            $table->index(['event_tag_id', 'event_id']);
        });

        Schema::create('event_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_record_id')->nullable()->constrained('event_records')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('usage', 20);
            $table->string('context', 20)->nullable();
            $table->string('disk', 50)->default('local');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'usage']);
            $table->index(['event_record_id', 'usage']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_files');
        Schema::dropIfExists('event_tag_relations');
        Schema::dropIfExists('event_tags');
        Schema::dropIfExists('event_records');
        Schema::dropIfExists('events');
    }
};
