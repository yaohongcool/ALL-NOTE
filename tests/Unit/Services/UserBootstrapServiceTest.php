<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserBootstrapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBootstrapServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_bootstrap_creates_default_password(): void
    {
        $user = User::create([
            'username' => 'bootstrap-user',
            'password' => bcrypt('Password@123'),
        ]);

        $service = $this->app->make(UserBootstrapService::class);
        $service->bootstrap($user);

        $this->assertDatabaseHas('passwords', [
            'user_id' => $user->id,
            'name' => 'GitHub（演示数据）',
            'account' => 'demo@example.com',
        ]);
    }

    public function test_bootstrap_creates_default_asset(): void
    {
        $user = User::create([
            'username' => 'bootstrap-user',
            'password' => bcrypt('Password@123'),
        ]);

        $service = $this->app->make(UserBootstrapService::class);
        $service->bootstrap($user);

        $this->assertDatabaseHas('assets', [
            'user_id' => $user->id,
            'name' => '个人电脑（演示数据）',
            'category' => '物理设备',
        ]);
    }

    public function test_bootstrap_creates_default_document(): void
    {
        $user = User::create([
            'username' => 'bootstrap-user',
            'password' => bcrypt('Password@123'),
        ]);

        $service = $this->app->make(UserBootstrapService::class);
        $service->bootstrap($user);

        $this->assertDatabaseHas('documents', [
            'user_id' => $user->id,
            'name' => '会员卡（演示数据）',
            'category' => '会员',
        ]);
    }
}
