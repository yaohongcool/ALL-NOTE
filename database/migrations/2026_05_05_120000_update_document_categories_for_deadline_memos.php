<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('documents')->where('category', '身份证')->update(['category' => '证件']);
        DB::table('documents')->where('category', '驾驶证')->update(['category' => '会员']);
        DB::table('documents')->where('category', '护照')->update(['category' => '物品']);
    }

    public function down(): void
    {
        DB::table('documents')->where('category', '证件')->update(['category' => '身份证']);
        DB::table('documents')->where('category', '会员')->update(['category' => '驾驶证']);
        DB::table('documents')->where('category', '物品')->update(['category' => '护照']);
    }
};
