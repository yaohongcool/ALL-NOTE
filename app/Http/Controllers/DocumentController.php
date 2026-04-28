<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document\StoreDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected array $categories = [
        '身份证',
        '驾驶证',
        '护照',
        '其它',
    ];

    public function index(Request $request): View
    {
        $user = auth()->user();
        $category = trim((string) $request->string('category'));
        $status = trim((string) $request->string('status'));
        $sort = $request->string('sort')->toString() ?: 'updated_desc';

        $query = $user->documents()
            ->when($category !== '', function ($query) use ($category) {
                $query->where('category', $category);
            });

        $query = match ($sort) {
            'name_asc' => $query->orderBy('name')->orderByDesc('id'),
            'name_desc' => $query->orderByDesc('name')->orderByDesc('id'),
            'updated_asc' => $query->orderBy('updated_at')->orderBy('id'),
            default => $query->orderByDesc('updated_at')->orderByDesc('id'),
        };

        $documents = $query->paginate(10)->withQueryString();

        if ($status !== '') {
            $documents->setCollection(
                $documents->getCollection()->filter(fn (Document $document) => $document->computed_status === $status)->values()
            );
        }

        return view('documents.index', [
            'documents' => $documents,
            'category' => $category,
            'status' => $status,
            'sort' => $sort,
            'categories' => $this->categories,
            'statuses' => ['正常', '即将到期', '已过期'],
        ]);
    }

    public function create(): View
    {
        return view('documents.create', [
            'document' => new Document([
                'category' => '身份证',
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
            'due_date' => $data['due_date'] ?: null,
            'note' => $data['note'] ?: null,
        ]);

        return redirect()->route('documents.index')
            ->with('success', '证照记录已创建。');
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
            'due_date' => $data['due_date'] ?: null,
            'note' => $data['note'] ?: null,
        ]);

        return redirect()->route('documents.index')
            ->with('success', '证照记录已更新。');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorizeDocument($document);

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', '证照记录已删除。');
    }

    protected function authorizeDocument(Document $document): void
    {
        abort_unless($document->user_id === auth()->id(), 403);
    }

    protected function computeStatus(?string $dueDate): string
    {
        if (! $dueDate) {
            return '正常';
        }

        $today = Carbon::today();
        $due = Carbon::parse($dueDate)->startOfDay();
        $days = $today->diffInDays($due, false);

        if ($days < 0) {
            return '已过期';
        }

        if ($days <= 60) {
            return '即将到期';
        }

        return '正常';
    }
}