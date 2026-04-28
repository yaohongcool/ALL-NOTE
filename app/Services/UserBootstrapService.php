<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class UserBootstrapService
{
    public function __construct(
        protected PasswordCipherService $passwordCipherService
    ) {
    }

    public function bootstrap(User $user): void
    {
        $this->createDefaultPassword($user);
        $this->createDefaultAsset($user);
        $this->createDefaultDocument($user);
    }

    protected function createDefaultPassword(User $user): void
    {
        $user->passwords()->create([
            'name' => 'GitHub（演示数据）',
            'account' => 'demo@example.com',
            'encrypted_password' => $this->passwordCipherService->encrypt('GitHub@123'),
            'phone' => null,
            'email' => null,
            'note' => '演示数据',
        ]);
    }

    protected function createDefaultAsset(User $user): void
    {
        $user->assets()->create([
            'category' => '物理设备',
            'name' => '个人电脑（演示数据）',
            'status' => '正常',
            'due_date' => null,
            'details_json' => [
                'cpu_model' => 'i5-10400F',
                'gpu_model' => 'RTX 4060',
                'memory' => '16',
                'storage_1' => 'SSD 1',
                'storage_2' => 'SSD 2',
                'storage_3' => null,
            ],
            'note' => '演示数据',
        ]);
    }

    protected function createDefaultDocument(User $user): void
    {
        $createdAt = $user->created_at instanceof Carbon
            ? $user->created_at->copy()
            : Carbon::parse($user->created_at);

        $user->documents()->create([
            'name' => '张三（演示数据）',
            'category' => '身份证',
            'status' => '即将过期',
            'due_date' => $createdAt->addDays(30)->toDateString(),
            'note' => '演示数据',
        ]);
    }
}