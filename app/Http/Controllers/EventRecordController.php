<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\StoreEventRecordRequest;
use App\Http\Requests\Event\UpdateEventRecordRequest;
use App\Models\Event;
use App\Models\EventFile;
use App\Models\EventRecord;
use App\Services\EventContentService;
use App\Services\EventFileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class EventRecordController extends Controller
{
    public function __construct(
        protected EventFileService $eventFileService,
        protected EventContentService $eventContentService
    ) {
    }

    public function create(Event $event): View
    {
        $this->authorizeEventOwner($event);

        return view('events.records.create', [
            'event' => $event,
            'record' => new EventRecord(),
        ]);
    }

    public function store(StoreEventRecordRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventOwner($event);

        $user = auth()->user();
        $data = $request->validated();

        DB::transaction(function () use ($event, $user, $data, $request) {
            $record = $event->records()->create([
                'user_id' => $user->id,
                'process' => null,
                'result' => null,
            ]);

            $fileIdByKey = $this->eventFileService->storeRecordUploads($event, $record, $user, $request);

            $record->update([
                'process' => $this->eventContentService->replaceUploadKeys($data['process'] ?? null, $fileIdByKey[EventFile::CONTEXT_PROCESS] ?? []),
                'result' => $this->eventContentService->replaceUploadKeys($data['result'] ?? null, $fileIdByKey[EventFile::CONTEXT_RESULT] ?? []),
            ]);

            $this->eventFileService->deleteUnreferencedInlineFiles($record->fresh(['files']));
        });

        return redirect()->route('events.show', $event)
            ->with('success', '处理记录已添加。');
    }

    public function edit(EventRecord $eventRecord): View
    {
        $this->authorizeRecordOwner($eventRecord);
        $eventRecord->load(['event', 'files']);

        return view('events.records.edit', [
            'event' => $eventRecord->event,
            'record' => $eventRecord,
        ]);
    }

    public function update(UpdateEventRecordRequest $request, EventRecord $eventRecord): RedirectResponse
    {
        $this->authorizeRecordOwner($eventRecord);
        $eventRecord->load('event');

        $user = auth()->user();
        $data = $request->validated();

        DB::transaction(function () use ($eventRecord, $user, $data, $request) {
            $this->deleteSelectedFiles($eventRecord, $data['delete_file_ids'] ?? []);
            $fileIdByKey = $this->eventFileService->storeRecordUploads($eventRecord->event, $eventRecord, $user, $request);

            $eventRecord->update([
                'process' => $this->eventContentService->replaceUploadKeys($data['process'] ?? null, $fileIdByKey[EventFile::CONTEXT_PROCESS] ?? []),
                'result' => $this->eventContentService->replaceUploadKeys($data['result'] ?? null, $fileIdByKey[EventFile::CONTEXT_RESULT] ?? []),
            ]);

            $this->eventFileService->deleteUnreferencedInlineFiles($eventRecord->fresh(['files']));
        });

        return redirect()->route('events.show', $eventRecord->event)
            ->with('success', '处理记录已更新。');
    }

    public function destroy(EventRecord $eventRecord): RedirectResponse
    {
        $this->authorizeRecordOwner($eventRecord);
        $event = $eventRecord->event;

        $eventRecord->delete();

        return redirect()->route('events.show', $event)
            ->with('success', '处理记录已删除。');
    }

    protected function authorizeEventOwner(Event $event): void
    {
        abort_unless($event->isOwner(auth()->id()), 403);
    }

    protected function authorizeRecordOwner(EventRecord $record): void
    {
        abort_unless($record->isRecorder(auth()->id()), 403);
    }

    protected function deleteSelectedFiles(EventRecord $record, array $fileIds): void
    {
        $ids = collect($fileIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $record->files()
            ->whereIn('id', $ids)
            ->get()
            ->each(fn ($file) => $this->eventFileService->delete($file));
    }
}
