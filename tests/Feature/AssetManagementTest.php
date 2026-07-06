<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $username = 'asset-user'): User
    {
        return User::create([
            'username' => $username,
            'password' => Hash::make('Password@123'),
        ]);
    }

    public function test_index_page_shows_empty_state(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('assets.index'))
            ->assertOk()
            ->assertSee('暂无IT资产记录')
            ->assertSee('添加IT资产');
    }

    public function test_index_page_lists_assets(): void
    {
        $user = $this->createUser('asset-list-user');
        $user->assets()->create([
            'name' => '测试服务器',
            'category' => '云服务器',
            'status' => '正常',
            'due_date' => Carbon::today('Asia/Shanghai')->addDays(30)->toDateString(),
            'details_json' => ['ip_address' => '10.0.0.1', 'provider' => '阿里云'],
            'note' => '应用服务器',
        ]);

        $this->actingAs($user)->get(route('assets.index'))
            ->assertOk()
            ->assertSee('测试服务器')
            ->assertSee('云服务器')
            ->assertSee('应用服务器')
            ->assertSee('编辑')
            ->assertSee('删除');
    }

    public function test_user_data_is_isolated_in_asset_list(): void
    {
        $owner = User::create(['username' => 'asset-owner', 'password' => Hash::make('Password@123')]);
        $other = User::create(['username' => 'asset-other', 'password' => Hash::make('Password@123')]);

        $owner->assets()->create([
            'name' => '仅本人可见资产',
            'category' => '物理设备',
            'status' => '正常',
        ]);

        $this->actingAs($other)->get(route('assets.index'))
            ->assertOk()
            ->assertDontSee('仅本人可见资产');
    }

    public function test_create_page_renders(): void
    {
        $user = $this->createUser('asset-create-page');

        $this->actingAs($user)->get(route('assets.create'))
            ->assertOk()
            ->assertSee('创建IT资产记录')
            ->assertSee('添加物理设备、云服务器、域名信息')
            ->assertSee('保存IT资产')
            ->assertSee('物理设备')
            ->assertSee('云服务器')
            ->assertSee('域名');
    }

    public function test_user_can_create_physical_device_asset(): void
    {
        $user = $this->createUser('asset-physical');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('assets.store'), [
            '_token' => 'test-token',
            'category' => '物理设备',
            'name' => '办公电脑',
            'due_date' => '',
            'cpu_model' => 'i7-13700',
            'gpu_model' => 'RTX 4060',
            'memory' => '32',
            'storage_1' => '1',
            'storage_2' => '',
            'storage_3' => '',
            'note' => '主办公电脑',
        ]);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success', '资产记录已创建。');

        $this->assertDatabaseHas('assets', [
            'user_id' => $user->id,
            'category' => '物理设备',
            'name' => '办公电脑',
            'status' => '正常',
            'due_date' => null,
            'note' => '主办公电脑',
        ]);

        $asset = $user->assets()->first();
        $this->assertSame('i7-13700', $asset->getDetail('cpu_model'));
        $this->assertSame('RTX 4060', $asset->getDetail('gpu_model'));
        $this->assertSame('32', $asset->getDetail('memory'));
    }

    public function test_user_can_create_cloud_server_asset(): void
    {
        $user = $this->createUser('asset-cloud');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('assets.store'), [
            '_token' => 'test-token',
            'category' => '云服务器',
            'name' => '生产服务器',
            'due_date' => Carbon::today('Asia/Shanghai')->addDays(90)->toDateString(),
            'cpu_cores' => '8',
            'memory_size' => '16GB',
            'ip_address' => '192.168.1.100',
            'operating_system' => 'Ubuntu 24.04',
            'provider' => '腾讯云',
        ]);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success', '资产记录已创建。');

        $this->assertDatabaseHas('assets', [
            'name' => '生产服务器',
            'category' => '云服务器',
            'status' => '正常',
        ]);
    }

    public function test_cloud_server_requires_due_date(): void
    {
        $user = $this->createUser('asset-req-date');

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('assets.store'), [
            '_token' => 'test-token',
            'category' => '云服务器',
            'name' => '缺到期日',
            'due_date' => '',
        ])->assertSessionHasErrors('due_date');
    }

    public function test_user_can_create_domain_asset(): void
    {
        $user = $this->createUser('asset-domain');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('assets.store'), [
            '_token' => 'test-token',
            'category' => '域名',
            'name' => 'example.com',
            'due_date' => Carbon::today('Asia/Shanghai')->addDays(365)->toDateString(),
            'domain_address' => 'example.com',
        ]);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success', '资产记录已创建。');

        $this->assertDatabaseHas('assets', [
            'name' => 'example.com',
            'category' => '域名',
        ]);
    }

    public function test_domain_requires_domain_address(): void
    {
        $user = $this->createUser('asset-req-addr');

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('assets.store'), [
            '_token' => 'test-token',
            'category' => '域名',
            'name' => '缺地址',
            'due_date' => '2026-12-31',
            'domain_address' => '',
        ])->assertSessionHasErrors('domain_address');
    }

    public function test_asset_status_is_computed_as_expired_when_past_due(): void
    {
        $user = $this->createUser('asset-expired');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('assets.store'), [
            '_token' => 'test-token',
            'category' => '域名',
            'name' => '过期域名',
            'due_date' => Carbon::today('Asia/Shanghai')->subDay()->toDateString(),
            'domain_address' => 'expired.com',
        ]);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assets', [
            'name' => '过期域名',
            'status' => '已过期',
        ]);
    }

    public function test_asset_status_is_computed_as_impending_within_60_days(): void
    {
        $user = $this->createUser('asset-impending');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('assets.store'), [
            '_token' => 'test-token',
            'category' => '域名',
            'name' => '即将到期域名',
            'due_date' => Carbon::today('Asia/Shanghai')->addDays(30)->toDateString(),
            'domain_address' => 'impending.com',
        ]);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assets', [
            'name' => '即将到期域名',
            'status' => '即将到期',
        ]);
    }

    public function test_user_can_edit_asset(): void
    {
        $user = $this->createUser('asset-editor');
        $asset = $user->assets()->create([
            'name' => '旧资产',
            'category' => '物理设备',
            'status' => '正常',
        ]);

        $this->actingAs($user)->get(route('assets.edit', $asset))
            ->assertOk()
            ->assertSee('旧资产')
            ->assertSee('物理设备');
    }

    public function test_user_can_update_asset(): void
    {
        $user = $this->createUser('asset-updater');
        $asset = $user->assets()->create([
            'name' => '旧名称',
            'category' => '物理设备',
            'status' => '正常',
            'due_date' => null,
        ]);

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->put(route('assets.update', $asset), [
            '_token' => 'test-token',
            'category' => '物理设备',
            'name' => '新名称',
            'cpu_model' => '新CPU',
            'due_date' => '',
        ]);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success');

        $asset->refresh();
        $this->assertSame('新名称', $asset->name);
        $this->assertSame('新CPU', $asset->getDetail('cpu_model'));
    }

    public function test_user_can_delete_asset(): void
    {
        $user = $this->createUser('asset-deleter');
        $asset = $user->assets()->create([
            'name' => '待删除资产',
            'category' => '物理设备',
            'status' => '正常',
        ]);

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->delete(route('assets.destroy', $asset), [
            '_token' => 'test-token',
        ])->assertRedirect(route('assets.index'))
            ->assertSessionHas('success', '资产记录已删除。');

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    }

    public function test_other_user_cannot_access_asset(): void
    {
        $owner = User::create(['username' => 'asset-auth-owner', 'password' => Hash::make('Password@123')]);
        $other = User::create(['username' => 'asset-auth-other', 'password' => Hash::make('Password@123')]);
        $asset = $owner->assets()->create([
            'name' => '他人资产',
            'category' => '物理设备',
            'status' => '正常',
        ]);

        $this->actingAs($other)->get(route('assets.edit', $asset))
            ->assertForbidden();

        $this->actingAs($other)->withSession(['_token' => 'test-token'])->put(route('assets.update', $asset), [
            '_token' => 'test-token',
            'category' => '物理设备',
            'name' => '篡改',
        ])->assertForbidden();

        $this->actingAs($other)->withSession(['_token' => 'test-token'])->delete(route('assets.destroy', $asset), [
            '_token' => 'test-token',
        ])->assertForbidden();
    }

    public function test_asset_index_has_responsive_table_markup(): void
    {
        $user = $this->createUser('asset-responsive');
        $user->assets()->create([
            'name' => '响应式资产',
            'category' => '物理设备',
            'status' => '正常',
        ]);

        $this->actingAs($user)->get(route('assets.index'))
            ->assertOk()
            ->assertSee('responsive-table-wrap', false)
            ->assertSee('responsive-table', false)
            ->assertSee('data-label="操作"', false)
            ->assertSee('data-label="名称"', false)
            ->assertSee('data-label="分类"', false)
            ->assertSee('data-label="状态"', false)
            ->assertSee('data-label="信息"', false)
            ->assertSee('data-label="到期日期"', false)
            ->assertSee('data-label="距离到期"', false);
    }
}
