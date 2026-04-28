<?php

namespace App\Http\Controllers;

use App\Models\EventFile;
use App\Services\EventFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventFileController extends Controller
{
    public function __construct(
        protected EventFileService $eventFileService
    ) {
    }

    public function show(EventFile $eventFile): StreamedResponse
    {
        $this->authorizeFile($eventFile);

        return Storage::disk($eventFile->disk)->response(
            $eventFile->path,
            $eventFile->original_name,
            $this->headers($eventFile),
            'inline'
        );
    }

    public function download(EventFile $eventFile): StreamedResponse
    {
        $this->authorizeFile($eventFile);

        return Storage::disk($eventFile->disk)->download(
            $eventFile->path,
            $eventFile->original_name,
            $this->headers($eventFile)
        );
    }

    public function destroy(EventFile $eventFile): JsonResponse
    {
        $eventFile->loadMissing('record');

        abort_unless($eventFile->isAttachment(), 403);
        abort_unless($eventFile->record?->isRecorder(auth()->id()), 403);

        $this->eventFileService->delete($eventFile);

        return response()->json([
            'message' => '附件已删除。',
        ]);
    }

    protected function authorizeFile(EventFile $eventFile): void
    {
        $eventFile->loadMissing('event');

        abort_unless($eventFile->event?->isOwner(auth()->id()) || $eventFile->event?->isPublic(), 403);
        abort_unless(Storage::disk($eventFile->disk)->exists($eventFile->path), Response::HTTP_NOT_FOUND);
    }

    /**
     * @return array<string, string>
     */
    protected function headers(EventFile $eventFile): array
    {
        return array_filter([
            'Content-Type' => $eventFile->mime_type,
        ]);
    }
}
