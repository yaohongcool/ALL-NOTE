<?php

namespace Tests\Unit\Services;

use App\Models\EventRecord;
use App\Services\EventContentService;
use Tests\TestCase;

class EventContentServiceTest extends TestCase
{
    public function test_normalize_null_returns_empty_document(): void
    {
        $service = $this->app->make(EventContentService::class);

        $result = $service->normalize(null);

        $this->assertSame('event_record_content', $result['type']);
        $this->assertSame(1, $result['version']);
        $this->assertSame([], $result['blocks']);
    }

    public function test_normalize_non_json_string_falls_back_to_document_from_text(): void
    {
        $service = $this->app->make(EventContentService::class);
        $text = '这是一段纯文本内容';

        $result = $service->normalize($text);

        $this->assertCount(1, $result['blocks']);
        $this->assertSame('text', $result['blocks'][0]['type']);
        $this->assertSame('这是一段纯文本内容', $result['blocks'][0]['text']);
    }

    public function test_normalize_invalid_blocks_array_falls_back(): void
    {
        $service = $this->app->make(EventContentService::class);
        $json = json_encode(['blocks' => 'not-an-array', 'type' => 'event_record_content']);

        $result = $service->normalize($json);

        $this->assertCount(1, $result['blocks']);
        $this->assertSame('text', $result['blocks'][0]['type']);
    }

    public function test_text_summary_returns_dash_for_empty(): void
    {
        $service = $this->app->make(EventContentService::class);

        $result = $service->textSummary(null);

        $this->assertSame('-', $result);

        $result = $service->textSummary(json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [],
        ]));

        $this->assertSame('-', $result);
    }

    public function test_text_summary_truncates_long_text(): void
    {
        $service = $this->app->make(EventContentService::class);
        $longText = '这是一段非常长的文本内容用于测试截断功能';
        $content = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => $longText],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $result = $service->textSummary($content, 5);

        $this->assertSame('这是一段非...', $result);
    }

    public function test_referenced_file_ids_extracts_image_block_ids(): void
    {
        $service = $this->app->make(EventContentService::class);
        $content = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => '文本块'],
                ['type' => 'image', 'file_id' => 10],
                ['type' => 'image', 'file_id' => 20],
                ['type' => 'image', 'file_id' => '30'],
                ['type' => 'image', 'key' => 'some-key'],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $result = $service->referencedFileIds($content);

        $this->assertCount(3, $result);
        $this->assertContains(10, $result);
        $this->assertContains(20, $result);
        $this->assertContains(30, $result);
    }

    public function test_render_markdown_strips_html(): void
    {
        $service = $this->app->make(EventContentService::class);
        $record = new EventRecord();
        $content = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => '<script>alert(1)</script>'],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $result = $service->renderDisplay($content, $record)->toHtml();

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('alert(1)', $result);
    }

    public function test_render_markdown_strips_javascript_links(): void
    {
        $service = $this->app->make(EventContentService::class);
        $record = new EventRecord();
        $content = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => '[link](javascript:alert(1))'],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $result = $service->renderDisplay($content, $record)->toHtml();

        $this->assertStringNotContainsString('javascript:', $result);
    }

    public function test_render_markdown_preserves_valid_markdown(): void
    {
        $service = $this->app->make(EventContentService::class);
        $record = new EventRecord();
        $content = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => "## 标题\n\n**加粗文本**\n\n`代码`"],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $result = $service->renderDisplay($content, $record)->toHtml();

        $this->assertStringContainsString('<h2>标题</h2>', $result);
        $this->assertStringContainsString('<strong>加粗文本</strong>', $result);
        $this->assertStringContainsString('<code>代码</code>', $result);
    }

    public function test_replace_upload_keys_handles_orphan_key(): void
    {
        $service = $this->app->make(EventContentService::class);
        $content = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'text', 'text' => '保留文本'],
                ['type' => 'image', 'key' => 'orphan-key'],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $result = $service->replaceUploadKeys($content, []);

        $this->assertNotNull($result);
        $decoded = json_decode($result, true);
        $this->assertCount(1, $decoded['blocks']);
        $this->assertSame('text', $decoded['blocks'][0]['type']);
        $this->assertSame('保留文本', $decoded['blocks'][0]['text']);
    }

    public function test_replace_upload_keys_all_keys_unmatched_returns_null(): void
    {
        $service = $this->app->make(EventContentService::class);
        $content = json_encode([
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => [
                ['type' => 'image', 'key' => 'key-a'],
                ['type' => 'image', 'key' => 'key-b'],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $result = $service->replaceUploadKeys($content, []);

        $this->assertNull($result);
    }
}
