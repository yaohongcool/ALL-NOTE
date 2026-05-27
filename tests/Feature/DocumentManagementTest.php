<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $username = 'doc-user'): User
    {
        return User::create([
            'username' => $username,
            'password' => Hash::make('Password@123'),
        ]);
    }

    public function test_index_page_shows_empty_state(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('documents.index'))
            ->assertOk()
            ->assertSee('暂无期限备忘')
            ->assertSee('添加期限备忘');
    }

    public function test_index_page_lists_documents(): void
    {
        $user = $this->createUser('doc-list-user');
        $user->documents()->create([
            'name' => '身份证',
            'category' => '证件',
            'status' => '正常',
            'due_date' => Carbon::today('Asia/Shanghai')->addDays(100)->toDateString(),
            'note' => '本人身份证',
        ]);

        $this->actingAs($user)->get(route('documents.index'))
            ->assertOk()
            ->assertSee('身份证')
            ->assertSee('证件')
            ->assertSee('本人身份证')
            ->assertSee('编辑')
            ->assertSee('删除');
    }

    public function test_user_data_is_isolated_in_document_list(): void
    {
        $owner = User::create(['username' => 'doc-owner', 'password' => Hash::make('Password@123')]);
        $other = User::create(['username' => 'doc-other', 'password' => Hash::make('Password@123')]);

        $owner->documents()->create([
            'name' => '仅本人可见备忘',
            'category' => '其它',
            'status' => '正常',
        ]);

        $this->actingAs($other)->get(route('documents.index'))
            ->assertOk()
            ->assertDontSee('仅本人可见备忘');
    }

    public function test_create_page_renders(): void
    {
        $user = $this->createUser('doc-create-page');

        $this->actingAs($user)->get(route('documents.create'))
            ->assertOk()
            ->assertSee('登记期限备忘')
            ->assertSee('当前支持证件、会员、物品、其它')
            ->assertSee('保存期限备忘')
            ->assertSee('证件')
            ->assertSee('会员')
            ->assertSee('物品')
            ->assertSee('其它');
    }

    public function test_user_can_create_document(): void
    {
        $user = $this->createUser('doc-creator');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('documents.store'), [
            '_token' => 'test-token',
            'name' => '驾驶证',
            'category' => '证件',
            'due_date' => Carbon::today('Asia/Shanghai')->addYears(5)->toDateString(),
            'note' => '机动车驾驶证',
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success', '期限备忘已创建。');

        $this->assertDatabaseHas('documents', [
            'user_id' => $user->id,
            'name' => '驾驶证',
            'category' => '证件',
            'status' => '正常',
            'note' => '机动车驾驶证',
        ]);
    }

    public function test_create_document_validates_required_fields(): void
    {
        $user = $this->createUser('doc-validator');

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('documents.store'), [
            '_token' => 'test-token',
            'name' => '',
            'category' => '',
        ])->assertSessionHasErrors(['name', 'category']);
    }

    public function test_document_status_computed_as_expired_when_past_due(): void
    {
        $user = $this->createUser('doc-expired');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('documents.store'), [
            '_token' => 'test-token',
            'name' => '过期会员',
            'category' => '会员',
            'due_date' => Carbon::today('Asia/Shanghai')->subDay()->toDateString(),
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', [
            'name' => '过期会员',
            'status' => '已过期',
        ]);
    }

    public function test_document_status_computed_as_impending_within_60_days(): void
    {
        $user = $this->createUser('doc-impending');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('documents.store'), [
            '_token' => 'test-token',
            'name' => '即将过期物品',
            'category' => '物品',
            'due_date' => Carbon::today('Asia/Shanghai')->addDays(15)->toDateString(),
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', [
            'name' => '即将过期物品',
            'status' => '即将到期',
        ]);
    }

    public function test_document_without_due_date_has_normal_status(): void
    {
        $user = $this->createUser('doc-no-due');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('documents.store'), [
            '_token' => 'test-token',
            'name' => '无期限备忘',
            'category' => '其它',
            'due_date' => '',
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', [
            'name' => '无期限备忘',
            'status' => '正常',
            'due_date' => null,
        ]);
    }

    public function test_user_can_edit_document(): void
    {
        $user = $this->createUser('doc-editor');
        $document = $user->documents()->create([
            'name' => '旧备忘',
            'category' => '其它',
            'status' => '正常',
        ]);

        $this->actingAs($user)->get(route('documents.edit', $document))
            ->assertOk()
            ->assertSee('旧备忘');
    }

    public function test_user_can_update_document(): void
    {
        $user = $this->createUser('doc-updater');
        $document = $user->documents()->create([
            'name' => '旧名称',
            'category' => '其它',
            'status' => '正常',
            'due_date' => null,
        ]);

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->put(route('documents.update', $document), [
            '_token' => 'test-token',
            'name' => '新名称',
            'category' => '证件',
            'due_date' => Carbon::today('Asia/Shanghai')->addDays(30)->toDateString(),
        ]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');

        $document->refresh();
        $this->assertSame('新名称', $document->name);
        $this->assertSame('证件', $document->category);
    }

    public function test_user_can_delete_document(): void
    {
        $user = $this->createUser('doc-deleter');
        $document = $user->documents()->create([
            'name' => '待删除备忘',
            'category' => '物品',
            'status' => '正常',
        ]);

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->delete(route('documents.destroy', $document), [
            '_token' => 'test-token',
        ])->assertRedirect(route('documents.index'))
            ->assertSessionHas('success', '期限备忘已删除。');

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    public function test_other_user_cannot_access_document(): void
    {
        $owner = User::create(['username' => 'doc-auth-owner', 'password' => Hash::make('Password@123')]);
        $other = User::create(['username' => 'doc-auth-other', 'password' => Hash::make('Password@123')]);
        $document = $owner->documents()->create([
            'name' => '他人备忘',
            'category' => '其它',
            'status' => '正常',
        ]);

        $this->actingAs($other)->get(route('documents.edit', $document))
            ->assertForbidden();

        $this->actingAs($other)->withSession(['_token' => 'test-token'])->put(route('documents.update', $document), [
            '_token' => 'test-token',
            'name' => '篡改',
            'category' => '其它',
        ])->assertForbidden();

        $this->actingAs($other)->withSession(['_token' => 'test-token'])->delete(route('documents.destroy', $document), [
            '_token' => 'test-token',
        ])->assertForbidden();
    }

    public function test_document_index_has_responsive_table_markup(): void
    {
        $user = $this->createUser('doc-responsive');
        $user->documents()->create([
            'name' => '响应式备忘',
            'category' => '证件',
            'status' => '正常',
        ]);

        $this->actingAs($user)->get(route('documents.index'))
            ->assertOk()
            ->assertSee('responsive-table-wrap', false)
            ->assertSee('responsive-table', false)
            ->assertSee('data-label="操作"', false)
            ->assertSee('data-label="名称"', false)
            ->assertSee('data-label="分类"', false)
            ->assertSee('data-label="状态"', false)
            ->assertSee('data-label="到期日期"', false)
            ->assertSee('data-label="距离到期"', false);
    }
}
