<?php

namespace App\Services;

use App\Enums\BlockType;
use App\Models\EventFile;
use App\Models\EventRecord;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class EventContentService
{
    private const DOCUMENT_TYPE = 'event_record_content';
    public function normalize(?string $content): array
    {
        if (! filled($content)) {
            return [
                'type' => self::DOCUMENT_TYPE,
                'version' => 1,
                'blocks' => [],
            ];
        }

        $decoded = json_decode((string) $content, true);

        if (! is_array($decoded)) {
            return $this->documentFromText((string) $content);
        }

        $blocks = $decoded['blocks'] ?? $decoded;
        if (! is_array($blocks)) {
            return $this->documentFromText((string) $content);
        }

        return [
            'type' => self::DOCUMENT_TYPE,
            'version' => 1,
            'blocks' => collect($blocks)
                ->map(fn ($block) => $this->normalizeBlock($block))
                ->filter()
                ->values()
                ->all(),
        ];
    }

    public function replaceUploadKeys(?string $content, array $fileIdByKey): ?string
    {
        $document = $this->normalize($content);

        $document['blocks'] = collect($document['blocks'])
            ->map(function (array $block) use ($fileIdByKey) {
                if (($block['type'] ?? null) !== BlockType::Image->value) {
                    return $block;
                }

                if (! empty($block['file_id'])) {
                    return [
                        'type' => BlockType::Image->value,
                        'file_id' => (int) $block['file_id'],
                    ];
                }

                $key = (string) ($block['key'] ?? '');
                if ($key !== '' && isset($fileIdByKey[$key])) {
                    return [
                        'type' => BlockType::Image->value,
                        'file_id' => (int) $fileIdByKey[$key],
                    ];
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();

        if (empty($document['blocks'])) {
            return null;
        }

        return json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function referencedFileIds(?string $content): array
    {
        return collect($this->normalize($content)['blocks'])
            ->where('type', BlockType::Image->value)
            ->pluck('file_id')
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function textSummary(?string $content, int $length = 30): string
    {
        $text = collect($this->normalize($content)['blocks'])
            ->where('type', BlockType::Text->value)
            ->pluck(BlockType::Text->value)
            ->filter()
            ->implode("\n");

        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        return $text === ''
            ? '-'
            : Str::substr($text, 0, $length) . '...';
    }

    private function renderBlocks(?string $content, EventRecord $record, ?string $context, string $emptyPlaceholder, \Closure $renderImage, \Closure $renderText): HtmlString
    {
        $files = $this->inlineFilesById($record, $context);
        $document = $this->normalize($content);
        $referencedFileIds = collect($document['blocks'])
            ->where('type', BlockType::Image->value)
            ->pluck('file_id')
            ->filter()
            ->map(fn ($id) => (int) $id);

        $blocks = collect($document['blocks'])
            ->merge(collect($files)
                ->reject(fn (EventFile $file) => $referencedFileIds->contains($file->id))
                ->map(fn (EventFile $file) => [
                    'type' => BlockType::Image->value,
                    'file_id' => $file->id,
                ]));

        $html = $blocks
            ->map(function (array $block) use ($files, $renderImage, $renderText) {
                if (($block['type'] ?? null) === BlockType::Image->value) {
                    $file = $files[(int) ($block['file_id'] ?? 0)] ?? null;
                    if (! $file) { return ''; }
                    return $renderImage($file);
                }

                $text = trim((string) ($block['text'] ?? ''));
                if ($text === '') { return ''; }
                return $renderText($text);
            })
            ->filter()
            ->implode("\n");

        return new HtmlString($html !== '' ? $html : $emptyPlaceholder);
    }

    public function renderDisplay(?string $content, EventRecord $record, ?string $context = null): HtmlString
    {
        return $this->renderBlocks(
            $content, $record, $context,
            '<p class="text-slate-400">-</p>',
            function (EventFile $file) {
                $src = e(route('event-files.show', $file));
                return <<<HTML
<figure class="my-4">
    <a href="{$src}" target="_blank" class="inline-block max-w-full">
        <img src="{$src}" alt="正文图片" class="rounded-xl border border-slate-200 bg-white object-contain dark:border-slate-800" style="max-width: min(100%, 560px); max-height: 360px; width: auto; height: auto;">
    </a>
</figure>
HTML;
            },
            function (string $text) {
                $html = trim($this->renderMarkdownText($text));
                return $html === '' ? '' : '<div class="event-markdown">' . $html . '</div>';
            }
        );
    }

    public function renderEditor(?string $content, EventRecord $record, ?string $context = null): HtmlString
    {
        return $this->renderBlocks(
            $content, $record, $context,
            '<p><br></p>',
            function (EventFile $file) {
                $src = e(route('event-files.show', $file));
                $fileId = (int) $file->id;
                return <<<HTML
<figure data-inline-image="1" contenteditable="false" class="my-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950">
    <a href="{$src}" target="_blank" class="inline-block max-w-full">
        <img src="{$src}" alt="正文图片" data-file-id="{$fileId}" class="rounded-xl object-contain" style="max-width: min(100%, 560px); max-height: 360px; width: auto; height: auto;">
    </a>
    <button type="button" data-remove-inline-image class="mt-2 rounded-xl border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/40">删除图片</button>
</figure>
HTML;
            },
            function (string $text) {
                return '<p>' . nl2br(e($text), false) . '</p>';
            }
        );
    }

    protected function documentFromText(string $text): array
    {
        $text = trim($text);

        return [
            'type' => self::DOCUMENT_TYPE,
            'version' => 1,
            'blocks' => $text === '' ? [] : [
                [
                    'type' => BlockType::Text->value,
                    BlockType::Text->value => $text,
                ],
            ],
        ];
    }

    protected function renderMarkdownText(string $text): string
    {
        return Str::markdown($text, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);
    }

    protected function normalizeBlock(mixed $block): ?array
    {
        if (! is_array($block)) {
            return null;
        }

        if (($block['type'] ?? null) === BlockType::Image->value) {
            if (isset($block['file_id']) && is_numeric($block['file_id'])) {
                return [
                    'type' => BlockType::Image->value,
                    'file_id' => (int) $block['file_id'],
                ];
            }

            $key = (string) Str::of((string) ($block['key'] ?? ''))->trim()->limit(80, '');

            return $key === '' ? null : [
                'type' => BlockType::Image->value,
                'key' => $key,
            ];
        }

        $text = (string) Str::of((string) ($block[BlockType::Text->value] ?? ''))
            ->replace("\r\n", "\n")
            ->replace("\r", "\n")
            ->trim();

        return $text === '' ? null : [
            'type' => BlockType::Text->value,
            BlockType::Text->value => $text,
        ];
    }

    protected function inlineFilesById(EventRecord $record, ?string $context = null): array
    {
        if (! $record->exists) {
            return [];
        }

        if (! $record->relationLoaded('files')) {
            $record->load('files');
        }

        return $record->files
            ->filter(fn (EventFile $file) => $file->isInline())
            ->when($context, fn ($files) => $files->where('context', $context))
            ->keyBy('id')
            ->all();
    }
}
