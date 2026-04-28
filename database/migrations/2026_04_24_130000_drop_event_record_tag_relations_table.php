<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('event_record_tag_relations');
    }

    public function down(): void
    {
        // Processing records no longer have their own tags.
    }
};
