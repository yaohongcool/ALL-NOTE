<?php

namespace App\Services;

use App\Models\EventFile;
use App\Models\EventRecord;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class EventContentService
{
    public function normalize(?string $content): array
    {
        if (! filled($content)) {
            return [
                'type' => 'event_record_content',
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
            'type' => 'event_record_content',
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
                if (($block['type'] ?? null) !== 'image') {
                    return $block;
                }

                if (! empty($block['file_id'])) {
                    return [
                        'type' => 'image',
                        'file_id' => (int) $block['file_id'],
                    ];
                }

                $key = (string) ($block['key'] ?? '');
                if ($key !== '' && isset($fileIdByKey[$key])) {
                    return [
                        'type' => 'image',
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
            ->where('type', 'image')
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
            ->where('type', 'text')
            ->pluck('text')
            ->filter()
            ->implode("\n");

        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        return $text === ''
            ? '-'
            : Str::substr($text, 0, $length) . '...';
    }

    public function renderDisplay(?string $content, EventRecord $record, ?string $context = null): HtmlString
    {
        $files = $this->inlineFilesById($record, $context);
        $document = $this->normalize($content);
        $referencedFileIds = collect($document['blocks'])->where('type', 'image')->pluck('file_id')->filter()->map(fn ($id) => (int) $id);
        $blocks = collect($document['blocks'])
            ->merge(collect($files)
                ->reject(fn (EventFile $file) => $referencedFileIds->contains($file->id))
                ->map(fn (EventFile $file) => [
                    'type' => 'image',
                    'file_id' => $file->id,
                ]));

        $html = $blocks
            ->map(function (array $block) use ($files) {
                if (($block['type'] ?? null) === 'image') {
                    $file = $files[(int) ($block['file_id'] ?? 0)] ?? null;

                    if (! $file) {
                        return '';
                    }

                    $src = e(route('event-files.show', $file));
                    $alt = '正文图片';

                    return <<<HTML
<figure class="my-4">
    <a href="{$src}" target="_blank" class="inline-block max-w-full">
        <img src="{$src}" alt="{$alt}" class="rounded-xl border border-slate-200 bg-white object-contain dark:border-slate-800" style="max-width: min(100%, 560px); max-height: 360px; width: auto; height: auto;">
    </a>
</figure>
HTML;
                }

                $text = trim((string) ($block['text'] ?? ''));
                if ($text === '') {
                    return '';
                }

                $html = trim($this->renderMarkdownText($text));

                return $html === ''
                    ? ''
                    : '<div class="event-markdown">' . $html . '</div>';
            })
            ->filter()
            ->implode("\n");

        return new HtmlString($html !== '' ? $html : '<p class="text-slate-400">-</p>');
    }

    public function renderEditor(?string $content, EventRecord $record, ?string $context = null): HtmlString
    {
        $files = $this->inlineFilesById($record, $context);
        $document = $this->normalize($content);
        $referencedFileIds = collect($document['blocks'])->where('type', 'image')->pluck('file_id')->filter()->map(fn ($id) => (int) $id);
        $blocks = collect($document['blocks'])
            ->merge(collect($files)
                ->reject(fn (EventFile $file) => $referencedFileIds->contains($file->id))
                ->map(fn (EventFile $file) => [
                    'type' => 'image',
                    'file_id' => $file->id,
                ]));

        $html = $blocks
            ->map(function (array $block) use ($files) {
                if (($block['type'] ?? null) === 'image') {
                    $file = $files[(int) ($block['file_id'] ?? 0)] ?? null;

                    if (! $file) {
                        return '';
                    }

                    $src = e(route('event-files.show', $file));
                    $alt = '正文图片';
                    $fileId = (int) $file->id;

                    return <<<HTML
<figure data-inline-image="1" contenteditable="false" class="my-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950">
    <a href="{$src}" target="_blank" class="inline-block max-w-full">
        <img src="{$src}" alt="{$alt}" data-file-id="{$fileId}" class="rounded-xl object-contain" style="max-width: min(100%, 560px); max-height: 360px; width: auto; height: auto;">
    </a>
    <button type="button" data-remove-inline-image class="mt-2 rounded-xl border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/40">删除图片</button>
</figure>
HTML;
                }

                $text = (string) ($block['text'] ?? '');
                if ($text === '') {
                    return '';
                }

                return '<p>' . nl2br(e($text), false) . '</p>';
            })
            ->filter()
            ->implode("\n");

        return new HtmlString($html !== '' ? $html : '<p><br></p>');
    }

    protected function documentFromText(string $text): array
    {
        $text = trim($text);

        return [
            'type' => 'event_record_content',
            'version' => 1,
            'blocks' => $text === '' ? [] : [
                [
                    'type' => 'text',
                    'text' => $text,
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

        if (($block['type'] ?? null) === 'image') {
            if (isset($block['file_id']) && is_numeric($block['file_id'])) {
                return [
                    'type' => 'image',
                    'file_id' => (int) $block['file_id'],
                ];
            }

            $key = (string) Str::of((string) ($block['key'] ?? ''))->trim()->limit(80, '');

            return $key === '' ? null : [
                'type' => 'image',
                'key' => $key,
            ];
        }

        $text = (string) Str::of((string) ($block['text'] ?? ''))
            ->replace("\r\n", "\n")
            ->replace("\r", "\n")
            ->trim();

        return $text === '' ? null : [
            'type' => 'text',
            'text' => $text,
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
