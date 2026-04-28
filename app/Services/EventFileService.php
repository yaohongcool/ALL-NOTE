<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventFile;
use App\Models\EventRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class EventFileService
{
    public function __construct(
        protected EventContentService $eventContentService
    ) {
    }

    public function storeRecordUploads(Event $event, EventRecord $record, User $user, Request $request): array
    {
        $processFiles = $this->storeFiles(
            $this->files($request, 'process_images'),
            $event,
            $record,
            $user,
            EventFile::USAGE_INLINE,
            EventFile::CONTEXT_PROCESS,
            $this->keys($request, 'process_image_keys')
        );

        $resultFiles = $this->storeFiles(
            $this->files($request, 'result_images'),
            $event,
            $record,
            $user,
            EventFile::USAGE_INLINE,
            EventFile::CONTEXT_RESULT,
            $this->keys($request, 'result_image_keys')
        );

        $this->storeFiles(
            $this->files($request, 'attachments'),
            $event,
            $record,
            $user,
            EventFile::USAGE_ATTACHMENT
        );

        return [
            EventFile::CONTEXT_PROCESS => $processFiles,
            EventFile::CONTEXT_RESULT => $resultFiles,
        ];
    }

    public function delete(EventFile $file): void
    {
        Storage::disk($file->disk)->delete($file->path);
        $file->delete();
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, string>  $keys
     * @return array<string, int>
     */
    protected function storeFiles(array $files, Event $event, EventRecord $record, User $user, string $usage, ?string $context = null, array $keys = []): array
    {
        $fileIdByKey = [];

        foreach ($files as $index => $file) {
            $path = $this->storeUploadedFile($file, $event);

            $eventFile = $record->files()->create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'usage' => $usage,
                'context' => $context,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            $key = $keys[$index] ?? null;
            if ($key) {
                $fileIdByKey[$key] = $eventFile->id;
            }
        }

        return $fileIdByKey;
    }

    public function deleteUnreferencedInlineFiles(EventRecord $record): void
    {
        $referencedFileIds = collect($this->eventContentService->referencedFileIds($record->process))
            ->merge($this->eventContentService->referencedFileIds($record->result))
            ->unique()
            ->values();

        $record->files()
            ->where('usage', EventFile::USAGE_INLINE)
            ->when($referencedFileIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $referencedFileIds))
            ->get()
            ->each(fn (EventFile $file) => $this->delete($file));
    }

    protected function storeUploadedFile(UploadedFile $file, Event $event): string
    {
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin';
        $filename = (string) Str::uuid() . '.' . strtolower($extension);
        $directory = 'event-files/' . $event->user_id . '/' . $event->id;
        $path = $file->storeAs($directory, $filename, 'local');

        if (! $path) {
            throw new RuntimeException('事件文件保存失败。');
        }

        return $path;
    }

    /**
     * @return array<int, UploadedFile>
     */
    protected function files(Request $request, string $key): array
    {
        $files = $request->file($key, []);

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (! is_array($files)) {
            return [];
        }

        return collect($files)
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function keys(Request $request, string $key): array
    {
        $decoded = json_decode((string) $request->input($key, '[]'), true);

        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }
}
