<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Models\EventFile;
use App\Models\EventRecord;
use App\Models\User;
use App\Services\EventContentService;
use App\Services\EventFileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct(
        protected EventFileService $eventFileService,
        protected EventContentService $eventContentService
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();

        $events = $user->events()
            ->with(['tags', 'summaryRecord'])
            ->withCount('records')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('events.index', [
            'events' => $events,
        ]);
    }

    public function create(): View
    {
        $user = auth()->user();

        return view('events.create', [
            'event' => new Event([
                'status' => Event::STATUS_PROCESSED,
                'visibility' => Event::VISIBILITY_PRIVATE,
            ]),
            'record' => new EventRecord(),
            'statuses' => Event::STATUSES,
            'tags' => $user->eventTags()->orderBy('name')->get(),
            'selectedEventTagIds' => [],
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        $event = DB::transaction(function () use ($data, $user, $request) {
            $event = $user->events()->create([
                'title' => $data['title'],
                'status' => $data['status'],
                'subject' => $data['subject'] ?: null,
                'occurred_on' => $data['occurred_on'] ?: null,
                'visibility' => $data['visibility'],
            ]);

            $record = $event->records()->create([
                'user_id' => $user->id,
                'process' => null,
                'result' => null,
            ]);

            $event->tags()->sync($this->resolveTagIds($user, $data['event_tag_ids'] ?? [], $data['new_event_tags'] ?? null));
            $fileIdByKey = $this->eventFileService->storeRecordUploads($event, $record, $user, $request);

            $record->update([
                'process' => $this->eventContentService->replaceUploadKeys($data['process'] ?? null, $fileIdByKey[EventFile::CONTEXT_PROCESS] ?? []),
                'result' => $this->eventContentService->replaceUploadKeys($data['result'] ?? null, $fileIdByKey[EventFile::CONTEXT_RESULT] ?? []),
            ]);

            $this->eventFileService->deleteUnreferencedInlineFiles($record->fresh(['files']));

            return $event;
        });

        return redirect()->route('events.show', $event)
            ->with('success', '事件已创建。');
    }

    public function show(Event $event): View
    {
        $this->authorizeViewEvent($event);

        $event->load([
            'user',
            'records' => fn ($query) => $query->latest('created_at')->latest('id'),
            'records.user',
            'records.files' => fn ($query) => $query->oldest('created_at')->oldest('id'),
        ]);

        if ($event->isOwner(auth()->id())) {
            $event->load('tags');
        }

        return view('events.show', [
            'event' => $event,
            'isOwner' => $event->isOwner(auth()->id()),
        ]);
    }

    public function edit(Event $event): View
    {
        $this->authorizeEventOwner($event);
        $event->load('tags');

        return view('events.edit', [
            'event' => $event,
            'statuses' => Event::STATUSES,
            'tags' => auth()->user()->eventTags()->orderBy('name')->get(),
            'selectedEventTagIds' => $event->tags->pluck('id')->all(),
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventOwner($event);

        $user = auth()->user();
        $data = $request->validated();

        DB::transaction(function () use ($event, $data, $user) {
            $event->update([
                'title' => $data['title'],
                'status' => $data['status'],
                'subject' => $data['subject'] ?: null,
                'occurred_on' => $data['occurred_on'] ?: null,
                'visibility' => $data['visibility'],
            ]);

            $event->tags()->sync($this->resolveTagIds($user, $data['event_tag_ids'] ?? [], $data['new_event_tags'] ?? null));
        });

        return redirect()->route('events.show', $event)
            ->with('success', '事件已更新。');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorizeEventOwner($event);

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', '事件已删除。');
    }

    protected function authorizeViewEvent(Event $event): void
    {
        abort_unless($event->isOwner(auth()->id()) || $event->isPublic(), 403);
    }

    protected function authorizeEventOwner(Event $event): void
    {
        abort_unless($event->isOwner(auth()->id()), 403);
    }

    protected function resolveTagIds(User $user, array $selectedIds, ?string $newTags): array
    {
        $selectedIds = collect($selectedIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $validSelectedIds = $user->eventTags()
            ->whereIn('id', $selectedIds)
            ->pluck('id');

        $newTagIds = collect($this->parseTagNames($newTags))
            ->map(fn (string $name) => $user->eventTags()->firstOrCreate(['name' => $name])->id);

        return $validSelectedIds
            ->merge($newTagIds)
            ->unique()
            ->values()
            ->all();
    }

    protected function parseTagNames(?string $newTags): array
    {
        if (! $newTags) {
            return [];
        }

        return collect(preg_split('/[,，;；\r\n]+/u', $newTags) ?: [])
            ->map(fn (string $name) => (string) Str::of($name)->trim()->squish()->limit(50, ''))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

}
