<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $username = 'password-user'): User
    {
        return User::create([
            'username' => $username,
            'password' => Hash::make('Password@123'),
        ]);
    }

    public function test_index_page_shows_empty_state(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('passwords.index'))
            ->assertOk()
            ->assertSee('暂无密码记录')
            ->assertSee('添加密码');
    }

    public function test_index_page_lists_passwords(): void
    {
        $user = $this->createUser('pwd-list-user');
        $user->passwords()->create([
            'name' => '测试账号',
            'account' => 'test@example.com',
            'encrypted_password' => 'encrypted-value',
            'phone' => '13800000000',
            'email' => 'bind@example.com',
            'note' => '备注信息',
        ]);

        $this->actingAs($user)->get(route('passwords.index'))
            ->assertOk()
            ->assertSee('测试账号')
            ->assertSee('test@example.com')
            ->assertSee('13800000000')
            ->assertSee('bind@example.com')
            ->assertSee('备注信息')
            ->assertSee('编辑')
            ->assertSee('删除');
    }

    public function test_user_data_is_isolated_in_password_list(): void
    {
        $owner = User::create(['username' => 'pwd-owner', 'password' => Hash::make('Password@123')]);
        $other = User::create(['username' => 'pwd-other', 'password' => Hash::make('Password@123')]);

        $owner->passwords()->create([
            'name' => '仅本人可见',
            'account' => 'owner@test.com',
            'encrypted_password' => 'encrypted',
        ]);

        $this->actingAs($other)->get(route('passwords.index'))
            ->assertOk()
            ->assertDontSee('仅本人可见')
            ->assertDontSee('owner@test.com');
    }

    public function test_create_page_renders(): void
    {
        $user = $this->createUser('pwd-create-page');

        $this->actingAs($user)->get(route('passwords.create'))
            ->assertOk()
            ->assertSee('创建密码记录')
            ->assertSee('所有密码内容将加密存储')
            ->assertSee('保存密码');
    }

    public function test_user_can_create_password(): void
    {
        $user = $this->createUser('pwd-creator');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('passwords.store'), [
            '_token' => 'test-token',
            'name' => 'GitHub',
            'account' => 'github-user',
            'password' => 'MySecret@123',
            'phone' => '13900000000',
            'email' => 'github@example.com',
            'note' => '个人 GitHub 账号',
        ]);

        $response->assertRedirect(route('passwords.index'));
        $response->assertSessionHas('success', '密码记录已创建。');

        $this->assertDatabaseHas('passwords', [
            'user_id' => $user->id,
            'name' => 'GitHub',
            'account' => 'github-user',
            'phone' => '13900000000',
            'email' => 'github@example.com',
            'note' => '个人 GitHub 账号',
        ]);

        $saved = $user->passwords()->first();
        $this->assertNotNull($saved->encrypted_password);
        $this->assertNotSame('MySecret@123', $saved->encrypted_password);
    }

    public function test_create_password_validates_required_fields(): void
    {
        $user = $this->createUser('pwd-validator');

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('passwords.store'), [
            '_token' => 'test-token',
            'name' => '',
            'account' => '',
            'password' => '',
        ])->assertSessionHasErrors(['name', 'account', 'password']);
    }

    public function test_create_password_accepts_optional_fields_as_empty(): void
    {
        $user = $this->createUser('pwd-min');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('passwords.store'), [
            '_token' => 'test-token',
            'name' => '最小记录',
            'account' => 'min-user',
            'password' => 'min123',
            'phone' => '',
            'email' => '',
            'note' => '',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('passwords', [
            'name' => '最小记录',
            'phone' => null,
            'email' => null,
            'note' => null,
        ]);
    }

    public function test_edit_page_renders_without_plain_password(): void
    {
        $user = $this->createUser('pwd-edit-page');
        $password = $user->passwords()->create([
            'name' => '可编辑密码',
            'account' => 'edit-user',
            'encrypted_password' => 'encrypted',
        ]);

        $this->actingAs($user)->get(route('passwords.edit', $password))
            ->assertOk()
            ->assertSee('可编辑密码')
            ->assertSee('edit-user')
            ->assertDontSee('encrypted');
    }

    public function test_user_can_update_password(): void
    {
        $user = $this->createUser('pwd-updater');
        $password = $user->passwords()->create([
            'name' => '旧名称',
            'account' => '旧账号',
            'encrypted_password' => '旧加密密码',
            'phone' => '13800000000',
            'email' => 'old@example.com',
            'note' => '旧备注',
        ]);

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->put(route('passwords.update', $password), [
            '_token' => 'test-token',
            'name' => '新名称',
            'account' => '新账号',
            'password' => '新密码@123',
            'phone' => '13900000001',
            'email' => 'new@example.com',
            'note' => '新备注',
        ]);

        $response->assertRedirect(route('passwords.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $password->refresh();
        $this->assertSame('新名称', $password->name);
        $this->assertSame('新账号', $password->account);
        $this->assertSame('13900000001', $password->phone);
        $this->assertSame('new@example.com', $password->email);
        $this->assertSame('新备注', $password->note);
        $this->assertNotSame('旧加密密码', $password->encrypted_password);
    }

    public function test_user_can_update_password_without_changing_password(): void
    {
        $user = $this->createUser('pwd-keep-pwd');
        $password = $user->passwords()->create([
            'name' => '不改密码',
            'account' => 'keep-pwd',
            'encrypted_password' => '原加密值',
        ]);
        $originalEncrypted = $password->encrypted_password;

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->put(route('passwords.update', $password), [
            '_token' => 'test-token',
            'name' => '名称已改',
            'account' => 'keep-pwd',
            'password' => '',
        ]);

        $response->assertRedirect(route('passwords.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        $password->refresh();
        $this->assertSame('名称已改', $password->name);
        $this->assertSame($originalEncrypted, $password->encrypted_password);
    }

    public function test_user_can_delete_password(): void
    {
        $user = $this->createUser('pwd-deleter');
        $password = $user->passwords()->create([
            'name' => '待删除',
            'account' => 'delete-me',
            'encrypted_password' => 'encrypted',
        ]);

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->delete(route('passwords.destroy', $password), [
            '_token' => 'test-token',
        ])->assertRedirect(route('passwords.index'))
            ->assertSessionHas('success', '密码记录已删除。');

        $this->assertDatabaseMissing('passwords', ['id' => $password->id]);
    }

    public function test_user_can_reveal_password(): void
    {
        $user = $this->createUser('pwd-revealer');
        $plaintext = '明文密码@123';
        $password = $user->passwords()->create([
            'name' => '可查看密码',
            'account' => 'reveal-user',
            'encrypted_password' => Crypt::encryptString($plaintext),
        ]);

        $this->actingAs($user)->postJson(route('passwords.reveal', $password))
            ->assertOk()
            ->assertExactJson([
                'password' => $plaintext,
            ]);
    }

    public function test_reveal_returns_decryption_failed_message_for_corrupted_data(): void
    {
        $user = $this->createUser('pwd-corrupted');
        $password = $user->passwords()->create([
            'name' => '损坏密码',
            'account' => 'corrupted',
            'encrypted_password' => '无效的加密数据',
        ]);

        $this->actingAs($user)->postJson(route('passwords.reveal', $password))
            ->assertStatus(422)
            ->assertJson([
                'message' => '读取密码失败，请稍后重试。',
            ]);
    }

    public function test_other_user_cannot_view_edit_delete_or_reveal_password(): void
    {
        $owner = User::create(['username' => 'pwd-auth-owner', 'password' => Hash::make('Password@123')]);
        $other = User::create(['username' => 'pwd-auth-other', 'password' => Hash::make('Password@123')]);
        $password = $owner->passwords()->create([
            'name' => '他人密码',
            'account' => 'other-account',
            'encrypted_password' => 'encrypted',
        ]);

        $this->actingAs($other)->get(route('passwords.edit', $password))
            ->assertForbidden();

        $this->actingAs($other)->withSession(['_token' => 'test-token'])->put(route('passwords.update', $password), [
            '_token' => 'test-token',
            'name' => '篡改名称',
            'account' => 'hacked',
        ])->assertForbidden();

        $this->actingAs($other)->withSession(['_token' => 'test-token'])->delete(route('passwords.destroy', $password), [
            '_token' => 'test-token',
        ])->assertForbidden();

        $this->actingAs($other)->postJson(route('passwords.reveal', $password))
            ->assertForbidden();
    }

    public function test_password_index_has_responsive_table_markup(): void
    {
        $user = $this->createUser('pwd-responsive');
        $user->passwords()->create([
            'name' => '响应式密码',
            'account' => 'responsive',
            'encrypted_password' => 'encrypted',
        ]);

        $this->actingAs($user)->get(route('passwords.index'))
            ->assertOk()
            ->assertSee('responsive-table-wrap', false)
            ->assertSee('responsive-table', false)
            ->assertSee('data-label="操作"', false)
            ->assertSee('data-label="名称"', false)
            ->assertSee('data-label="账号"', false)
            ->assertSee('data-label="密码"', false)
            ->assertSee('data-label="绑定手机/邮箱"', false)
            ->assertSee('data-label="备注"', false);
    }
}
