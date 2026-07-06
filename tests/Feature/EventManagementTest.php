<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventFile;
use App\Models\User;
use App\Services\EventContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_event_with_initial_record_and_tags(): void
    {
        Storage::fake('local');

        $user = User::create([
            'username' => 'event-user',
            'password' => Hash::make('Password@123'),
        ]);
        $processImageKey = 'process-image-1';
        $processContent = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => '排查登录日志。'],
                ['type' => 'image', 'key' => $processImageKey],
                ['type' => 'text', 'text' => '重置访问策略。'],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $resultContent = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => '异常登录已阻断。'],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('events.store'), [
            '_token' => 'test-token',
            'title' => '服务器登录异常处理',
            'description' => 'SSH 登录出现异常失败，影响生产服务器维护。',
            'status' => Event::STATUS_PROCESSED,
            'subject' => '生产服务器',
            'occurred_on' => null,
            'visibility' => Event::VISIBILITY_PRIVATE,
            'process' => $processContent,
            'result' => $resultContent,
            'new_event_tags' => '服务器, 安全',
            'process_images' => [
                UploadedFile::fake()->image('process.jpg'),
            ],
            'process_image_keys' => json_encode([$processImageKey]),
            'attachments' => [
                UploadedFile::fake()->create('report.pdf', 12, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $event = Event::first();

        $response->assertRedirect(route('events.show', $event));

        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
            'title' => '服务器登录异常处理',
            'description' => 'SSH 登录出现异常失败，影响生产服务器维护。',
            'status' => Event::STATUS_PROCESSED,
            'subject' => '生产服务器',
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);

        $record = $event->records()->first();
        $this->assertSame($user->id, $record->user_id);

        $processBlocks = app(EventContentService::class)->normalize($record->process)['blocks'];
        $this->assertSame('text', $processBlocks[0]['type']);
        $this->assertSame('排查登录日志。', $processBlocks[0]['text']);
        $this->assertSame('image', $processBlocks[1]['type']);
        $this->assertIsInt($processBlocks[1]['file_id']);
        $this->assertSame('text', $processBlocks[2]['type']);
        $this->assertSame('重置访问策略。', $processBlocks[2]['text']);

        $resultBlocks = app(EventContentService::class)->normalize($record->result)['blocks'];
        $this->assertSame('异常登录已阻断。', $resultBlocks[0]['text']);

        $this->assertSame(2, $event->tags()->count());
        $this->assertSame(2, EventFile::count());

        EventFile::all()->each(fn (EventFile $file) => Storage::disk('local')->assertExists($file->path));

        $this->actingAs($user)->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('问题描述')
            ->assertSee('SSH 登录出现异常失败，影响生产服务器维护。')
            ->assertSee('<img', false)
            ->assertDontSee('process.jpg')
            ->assertSee('report.pdf');
    }

    public function test_private_events_are_owner_only_and_public_events_hide_tags_from_other_users(): void
    {
        $owner = User::create([
            'username' => 'event-owner',
            'password' => Hash::make('Password@123'),
        ]);
        $viewer = User::create([
            'username' => 'event-viewer',
            'password' => Hash::make('Password@123'),
        ]);

        $privateEvent = $owner->events()->create([
            'title' => '私有事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $publicEvent = $owner->events()->create([
            'title' => '公开事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PUBLIC,
        ]);
        $tag = $owner->eventTags()->create([
            'name' => '内部标签',
        ]);
        $publicEvent->tags()->sync([$tag->id]);

        $this->actingAs($viewer)->get(route('events.show', $privateEvent))
            ->assertForbidden();

        $this->actingAs($viewer)->get(route('events.show', $publicEvent))
            ->assertOk()
            ->assertSee('公开事件')
            ->assertDontSee('内部标签');
    }

    public function test_event_record_display_renders_markdown_safely(): void
    {
        $user = User::create([
            'username' => 'markdown-user',
            'password' => Hash::make('Password@123'),
        ]);

        $event = $user->events()->create([
            'title' => 'Markdown 记录',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);

        $event->records()->create([
            'user_id' => $user->id,
            'process' => json_encode([
                'type' => 'event_record_content',
                'version' => 1,
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => implode("\n", [
                            '## 排查结果',
                            '',
                            '- **登录失败**',
                            '- `ssh`',
                            '',
                            '| 项目 | 值 |',
                            '| --- | --- |',
                            '| 状态 | 正常 |',
                            '',
                            '<script>alert("x")</script>',
                            '[危险链接](javascript:alert(1))',
                        ]),
                    ],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'result' => null,
        ]);

        $this->actingAs($user)->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('<h2>排查结果</h2>', false)
            ->assertSee('<strong>登录失败</strong>', false)
            ->assertSee('<code>ssh</code>', false)
            ->assertSee('<table>', false)
            ->assertDontSee('## 排查结果')
            ->assertDontSee('<script', false)
            ->assertDontSee('href="javascript:alert(1)"', false);
    }

    public function test_event_forms_only_show_event_tags_and_support_paste_handlers(): void
    {
        $user = User::create([
            'username' => 'form-user',
            'password' => Hash::make('Password@123'),
        ]);
        $event = $user->events()->create([
            'title' => '表单事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $tag = $user->eventTags()->create([
            'name' => '可删除标签',
        ]);

        $this->actingAs($user)->get(route('events.create'))
            ->assertOk()
            ->assertSee('事件标签')
            ->assertSee('问题描述')
            ->assertSee('可删除标签')
            ->assertSee('event-tags\/' . $tag->id, false)
            ->assertSee('deleteEventTag', false)
            ->assertSee('×')
            ->assertDontSee('处理记录标签')
            ->assertSee('eventRecordForm()', false)
            ->assertSee('contenteditable="true"', false)
            ->assertSee("handlePaste(\$event, 'process')", false)
            ->assertSee("handlePaste(\$event, 'result')", false)
            ->assertDontSee('选择过程图片')
            ->assertDontSee('选择结果图片');

        $this->actingAs($user)->get(route('events.edit', $event))
            ->assertOk()
            ->assertSee('问题描述');

        $this->actingAs($user)->get(route('event-records.create', $event))
            ->assertOk()
            ->assertDontSee('处理记录标签')
            ->assertSee('eventRecordForm()', false)
            ->assertSee('contenteditable="true"', false)
            ->assertSee("handlePaste(\$event, 'process')", false)
            ->assertSee("handlePaste(\$event, 'result')", false)
            ->assertDontSee('选择过程图片')
            ->assertDontSee('选择结果图片');
    }

    public function test_list_pages_hide_filters_and_keep_responsive_create_button(): void
    {
        $user = User::create([
            'username' => 'list-user',
            'password' => Hash::make('Password@123'),
        ]);
        $event = $user->events()->create([
            'title' => '列表按钮事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);

        $pages = [
            route('passwords.index') => '添加密码',
            route('assets.index') => '添加资产',
            route('documents.index') => '添加期限备忘',
            route('events.index') => '添加事件',
        ];

        foreach ($pages as $route => $createLabel) {
            $this->actingAs($user)->get($route)
                ->assertOk()
                ->assertSee($createLabel)
                ->assertSee('w-full items-center justify-center', false)
                ->assertSee('sm:w-auto', false)
                ->assertDontSee('onchange="this.form.submit()"', false)
                ->assertDontSee('重置')
                ->assertDontSee('查询')
                ->assertDontSee('name="category"', false)
                ->assertDontSee('name="status"', false)
                ->assertDontSee('name="sort"', false)
                ->assertDontSee('name="visibility"', false)
                ->assertDontSee('应用排序');
        }

        $this->actingAs($user)->get(route('events.index'))
            ->assertOk()
            ->assertSeeInOrder(['记录数', '内容', '操作'])
            ->assertSee('编辑')
            ->assertDontSee('查看')
            ->assertSee(route('events.show', $event), false)
            ->assertDontSee(route('events.edit', $event), false);

        $this->actingAs($user)->get(route('documents.create'))
            ->assertOk()
            ->assertSee('当前支持证件、会员、物品、其它')
            ->assertSee('<option value="其它"', false);
    }

    public function test_management_lists_render_mobile_responsive_table_markup(): void
    {
        $user = User::create([
            'username' => 'mobile-user',
            'password' => Hash::make('Password@123'),
        ]);

        $user->passwords()->create([
            'name' => '移动端账号',
            'account' => 'mobile@example.com',
            'encrypted_password' => 'encrypted',
            'phone' => '13800000000',
            'email' => 'mobile@example.com',
            'note' => '长备注用于验证移动端换行',
        ]);

        $user->assets()->create([
            'name' => '移动端资产',
            'category' => '云服务器',
            'status' => '正常',
            'due_date' => '2026-05-01',
            'details_json' => ['ip_address' => '192.168.1.1', 'provider' => '测试云'],
            'note' => '资产备注',
        ]);

        $user->documents()->create([
            'name' => '移动端期限备忘',
            'category' => '证件',
            'status' => '正常',
            'due_date' => '2026-05-01',
            'note' => '期限备忘备注',
        ]);

        $event = $user->events()->create([
            'title' => '移动端事件',
            'status' => Event::STATUS_PROCESSED,
            'subject' => '移动端来源',
            'occurred_on' => '2026-04-27',
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $event->records()->create([
            'user_id' => $user->id,
            'process' => '移动端过程',
            'result' => '移动端结果',
        ]);
        $tag = $user->eventTags()->create(['name' => '移动端标签']);
        $event->tags()->sync([$tag->id]);

        foreach ([route('dashboard'), route('events.index'), route('passwords.index'), route('assets.index'), route('documents.index')] as $route) {
            $this->actingAs($user)->get($route)
                ->assertOk()
                ->assertSee('responsive-table-wrap', false)
                ->assertSee('responsive-table', false)
                ->assertSee('data-label="操作"', false);
        }

        $this->actingAs($user)->get(route('events.index'))
            ->assertOk()
            ->assertSee('data-label="标题"', false)
            ->assertSee('data-label="内容"', false)
            ->assertDontSee('可见性');
    }

    public function test_dashboard_expiry_reminders_show_first_five_due_items(): void
    {
        $user = User::create([
            'username' => 'dashboard-reminder-user',
            'password' => Hash::make('Password@123'),
        ]);

        foreach (range(1, 6) as $index) {
            $user->documents()->create([
                'name' => '期限备忘 ' . $index,
                'category' => '物品',
                'status' => '正常',
                'due_date' => '2026-05-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
            ]);
        }

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('期限备忘')
            ->assertSee('期限备忘 1')
            ->assertSee('期限备忘 2')
            ->assertSee('期限备忘 3')
            ->assertSee('期限备忘 4')
            ->assertSee('期限备忘 5')
            ->assertDontSee('期限备忘 6');
    }

    public function test_user_can_quick_delete_own_event_tag(): void
    {
        $owner = User::create([
            'username' => 'tag-owner',
            'password' => Hash::make('Password@123'),
        ]);
        $other = User::create([
            'username' => 'tag-other',
            'password' => Hash::make('Password@123'),
        ]);

        $event = $owner->events()->create([
            'title' => '带标签事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $tag = $owner->eventTags()->create([
            'name' => '待删除标签',
        ]);
        $otherTag = $other->eventTags()->create([
            'name' => '他人标签',
        ]);
        $event->tags()->sync([$tag->id]);

        $this->actingAs($owner)
            ->withSession(['_token' => 'test-token'])
            ->deleteJson(route('event-tags.destroy', $otherTag), [], ['X-CSRF-TOKEN' => 'test-token'])
            ->assertForbidden();

        $this->actingAs($owner)
            ->withSession(['_token' => 'test-token'])
            ->deleteJson(route('event-tags.destroy', $tag), [], ['X-CSRF-TOKEN' => 'test-token'])
            ->assertOk()
            ->assertJson([
                'message' => '事件标签已删除。',
            ]);

        $this->assertDatabaseMissing('event_tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('event_tag_relations', ['event_tag_id' => $tag->id]);
    }

    public function test_recorder_can_quick_delete_attachment_without_confirmation(): void
    {
        Storage::fake('local');

        $owner = User::create([
            'username' => 'attachment-owner',
            'password' => Hash::make('Password@123'),
        ]);
        $other = User::create([
            'username' => 'attachment-other',
            'password' => Hash::make('Password@123'),
        ]);

        $event = $owner->events()->create([
            'title' => '附件事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $record = $event->records()->create([
            'user_id' => $owner->id,
            'process' => '处理过程',
            'result' => '处理结果',
        ]);
        $file = $record->files()->create([
            'event_id' => $event->id,
            'user_id' => $owner->id,
            'usage' => EventFile::USAGE_ATTACHMENT,
            'disk' => 'local',
            'path' => 'event-files/test/attachment.txt',
            'original_name' => 'attachment.txt',
            'mime_type' => 'text/plain',
            'size' => 5,
        ]);
        Storage::disk('local')->put($file->path, 'hello');

        $this->actingAs($owner)->get(route('event-records.edit', $record))
            ->assertOk()
            ->assertSee('data-event-attachment-id="' . $file->id . '"', false)
            ->assertSee('event-files\/' . $file->id, false)
            ->assertSee('deleteAttachment', false)
            ->assertSee('×')
            ->assertDontSee('name="delete_file_ids[]"', false);

        $this->actingAs($other)
            ->withSession(['_token' => 'test-token'])
            ->deleteJson(route('event-files.destroy', $file), [], ['X-CSRF-TOKEN' => 'test-token'])
            ->assertForbidden();

        $this->actingAs($owner)
            ->withSession(['_token' => 'test-token'])
            ->deleteJson(route('event-files.destroy', $file), [], ['X-CSRF-TOKEN' => 'test-token'])
            ->assertOk()
            ->assertJson([
                'message' => '附件已删除。',
            ]);

        Storage::disk('local')->assertMissing('event-files/test/attachment.txt');
        $this->assertDatabaseMissing('event_files', ['id' => $file->id]);
    }

    public function test_record_files_follow_event_visibility(): void
    {
        Storage::fake('local');

        $owner = User::create([
            'username' => 'file-owner',
            'password' => Hash::make('Password@123'),
        ]);
        $viewer = User::create([
            'username' => 'file-viewer',
            'password' => Hash::make('Password@123'),
        ]);

        $event = $owner->events()->create([
            'title' => '带文件事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $imageKey = 'private-image';
        $processContent = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => '处理过程'],
                ['type' => 'image', 'key' => $imageKey],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs($owner)->withSession(['_token' => 'test-token'])->post(route('event-records.store', $event), [
            '_token' => 'test-token',
            'process' => $processContent,
            'result' => '处理结果',
            'process_images' => [
                UploadedFile::fake()->image('private.jpg'),
            ],
            'process_image_keys' => json_encode([$imageKey]),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('events.show', $event));

        $file = EventFile::first();
        $this->assertNotNull($file);

        $this->actingAs($viewer)->get(route('event-files.show', $file))
            ->assertForbidden();

        $event->update(['visibility' => Event::VISIBILITY_PUBLIC]);

        $this->actingAs($viewer)->get(route('event-files.show', $file))
            ->assertOk();
    }

    public function test_recorder_can_update_and_delete_own_record_with_files(): void
    {
        Storage::fake('local');

        $user = User::create([
            'username' => 'record-owner',
            'password' => Hash::make('Password@123'),
        ]);

        $event = $user->events()->create([
            'title' => '记录维护事件',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);

        $record = $event->records()->create([
            'user_id' => $user->id,
            'process' => '旧过程',
            'result' => '旧结果',
        ]);

        $file = $record->files()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'usage' => EventFile::USAGE_ATTACHMENT,
            'disk' => 'local',
            'path' => 'event-files/test/report.txt',
            'original_name' => 'report.txt',
            'mime_type' => 'text/plain',
            'size' => 5,
        ]);
        Storage::disk('local')->put($file->path, 'hello');

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->put(route('event-records.update', $record), [
            '_token' => 'test-token',
            'process' => '新过程',
            'result' => '新结果',
            'delete_file_ids' => [$file->id],
            'attachments' => [
                UploadedFile::fake()->create('new-report.txt', 4, 'text/plain'),
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('events.show', $event));

        $updatedRecord = $record->fresh();
        $this->assertSame('新过程', app(EventContentService::class)->normalize($updatedRecord->process)['blocks'][0]['text']);
        $this->assertSame('新结果', app(EventContentService::class)->normalize($updatedRecord->result)['blocks'][0]['text']);
        Storage::disk('local')->assertMissing($file->path);
        $this->assertSame(1, $updatedRecord->files()->count());

        $this->actingAs($user)->withSession(['_token' => 'test-token'])->delete(route('event-records.destroy', $record), [
            '_token' => 'test-token',
        ])->assertRedirect(route('events.show', $event));

        $this->assertDatabaseMissing('event_records', ['id' => $record->id]);
        $this->assertSame(0, EventFile::count());
    }
}
