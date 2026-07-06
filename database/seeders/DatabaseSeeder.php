<?php

namespace Database\Seeders;

use App\Enums\AssetCategory;
use App\Enums\DocumentCategory;
use App\Enums\ExpiryStatus;
use App\Models\Asset;
use App\Models\Document;
use App\Models\Password;
use App\Models\User;
use App\Services\PasswordCipherService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'demo'],
            ['password' => Hash::make('Demo@123')]
        );

        $cipher = app(PasswordCipherService::class);

        if ($user->passwords()->count() === 0) {
            Password::create([
                'user_id' => $user->id,
                'name' => 'GitHub',
                'account' => 'demo@example.com',
                'encrypted_password' => $cipher->encrypt('GitHub@123'),
                'phone' => '13800138000',
                'email' => 'demo@example.com',
                'note' => '演示数据：GitHub 账号',
            ]);

            Password::create([
                'user_id' => $user->id,
                'name' => '服务器后台',
                'account' => 'root',
                'encrypted_password' => $cipher->encrypt('Server@123'),
                'phone' => null,
                'email' => 'ops@example.com',
                'note' => '演示数据：服务器管理账号',
            ]);
        }

        if ($user->assets()->count() === 0) {
            Asset::create([
                'user_id' => $user->id,
                'category' => AssetCategory::Physical->value,
                'name' => '办公主机',
                'status' => ExpiryStatus::Normal->value,
                'details_json' => [
                    'cpu_model' => 'Intel i7-12700',
                    'gpu_model' => 'RTX 3060',
                    'memory' => '32',
                    'storage_1' => '512',
                    'storage_2' => '1024',
                    'storage_3' => null,
                ],
                'note' => '演示数据：办公电脑',
            ]);

            Asset::create([
                'user_id' => $user->id,
                'category' => AssetCategory::Server->value,
                'name' => '生产服务器',
                'status' => ExpiryStatus::Expiring->value,
                'due_date' => now()->addDays(45)->toDateString(),
                'details_json' => [
                    'cpu_cores' => '4',
                    'memory_size' => '8GB',
                    'ip_address' => '192.168.1.100',
                    'operating_system' => 'Ubuntu 22.04',
                    'provider' => 'Alibaba Cloud',
                ],
                'note' => '演示数据：生产环境云主机',
            ]);

            Asset::create([
                'user_id' => $user->id,
                'category' => AssetCategory::Domain->value,
                'name' => '公司官网域名',
                'status' => ExpiryStatus::Expired->value,
                'due_date' => now()->subDays(5)->toDateString(),
                'details_json' => [
                    'domain_address' => 'example.com',
                ],
                'note' => '演示数据：官网域名',
            ]);
        }

        if ($user->documents()->count() === 0) {
            Document::create([
                'user_id' => $user->id,
                'name' => '身份证',
                'category' => DocumentCategory::Certificate->value,
                'status' => ExpiryStatus::Normal->value,
                'due_date' => now()->addYears(5)->toDateString(),
                'note' => '演示数据：证件',
            ]);

            Document::create([
                'user_id' => $user->id,
                'name' => '会员卡',
                'category' => DocumentCategory::Membership->value,
                'status' => ExpiryStatus::Expiring->value,
                'due_date' => now()->addDays(30)->toDateString(),
                'note' => '演示数据：会员',
            ]);

            Document::create([
                'user_id' => $user->id,
                'name' => '保修物品',
                'category' => DocumentCategory::Item->value,
                'status' => ExpiryStatus::Expired->value,
                'due_date' => now()->subDays(10)->toDateString(),
                'note' => '演示数据：物品',
            ]);
        }
    }
}
