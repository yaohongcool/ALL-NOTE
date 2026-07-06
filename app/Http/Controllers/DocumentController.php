<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCategory;
use App\Enums\ExpiryStatus;
use App\Http\Requests\Document\StoreDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    protected array $categories;

    public function __construct()
    {
        $this->categories = DocumentCategory::values();
    }

    public function index(): View
    {
        $user = auth()->user();

        $documents = $user->documents()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('documents.index', [
            'documents' => $documents,
        ]);
    }

    public function create(): View
    {
        return view('documents.create', [
            'document' => new Document([
                'category' => DocumentCategory::Certificate->value,
            ]),
            'categories' => $this->categories,
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        auth()->user()->documents()->create([
            'name' => $data['name'],
            'category' => $data['category'],
            'status' => $this->computeStatus($data['due_date'] ?? null),
            'due_date' => ($data['due_date'] ?? null),
            'note' => ($data['note'] ?? null),
        ]);

        return redirect()->route('documents.index')
            ->with('success', '期限备忘已创建。');
    }

    public function edit(Document $document): View
    {
        $this->authorizeDocument($document);

        return view('documents.edit', [
            'document' => $document,
            'categories' => $this->categories,
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->authorizeDocument($document);

        $data = $request->validated();

        $document->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'status' => $this->computeStatus($data['due_date'] ?? null),
            'due_date' => ($data['due_date'] ?? null),
            'note' => ($data['note'] ?? null),
        ]);

        return redirect()->route('documents.index')
            ->with('success', '期限备忘已更新。');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorizeDocument($document);

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', '期限备忘已删除。');
    }

    protected function authorizeDocument(Document $document): void
    {
        abort_unless($document->user_id === auth()->id(), 403);
    }

    protected function computeStatus(?string $dueDate): string
    {
        if (! $dueDate) {
            return ExpiryStatus::Normal->value;
        }

        $tz = config('app.display_timezone', 'Asia/Shanghai');
        $today = Carbon::today($tz);
        $due = Carbon::parse($dueDate, $tz)->startOfDay();
        $days = $today->diffInDays($due, false);

        if ($days < 0) {
            return ExpiryStatus::Expired->value;
        }

        if ($days <= 60) {
            return ExpiryStatus::Expiring->value;
        }

        return ExpiryStatus::Normal->value;
    }
}
