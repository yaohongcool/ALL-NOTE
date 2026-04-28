<?php

namespace App\Http\Controllers;

use App\Http\Requests\Asset\StoreAssetRequest;
use App\Http\Requests\Asset\UpdateAssetRequest;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected array $categories = [
        '物理设备',
        '云服务器',
        '域名',
    ];

    public function index(Request $request): View
    {
        $user = auth()->user();
        $category = trim((string) $request->string('category'));
        $status = trim((string) $request->string('status'));
        $sort = $request->string('sort')->toString() ?: 'updated_desc';

        $query = $user->assets()
            ->when($category !== '', function ($query) use ($category) {
                $query->where('category', $category);
            });

        $query = match ($sort) {
            'name_asc' => $query->orderBy('name')->orderByDesc('id'),
            'name_desc' => $query->orderByDesc('name')->orderByDesc('id'),
            'updated_asc' => $query->orderBy('updated_at')->orderBy('id'),
            default => $query->orderByDesc('updated_at')->orderByDesc('id'),
        };

        $assets = $query->paginate(10)->withQueryString();

        if ($status !== '') {
            $assets->setCollection(
                $assets->getCollection()->filter(fn (Asset $asset) => $asset->computed_status === $status)->values()
            );
        }

        return view('assets.index', [
            'assets' => $assets,
            'category' => $category,
            'status' => $status,
            'sort' => $sort,
            'categories' => $this->categories,
            'statuses' => ['正常', '即将到期', '已过期'],
        ]);
    }

    public function create(): View
    {
        return view('assets.create', [
            'asset' => new Asset([
                'category' => '物理设备',
                'details_json' => [],
            ]),
            'categories' => $this->categories,
        ]);
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $computedStatus = $this->computeStatus($data['due_date'] ?? null);

        auth()->user()->assets()->create([
            'category' => $data['category'],
            'name' => $data['name'],
            'status' => $computedStatus,
            'due_date' => $data['due_date'] ?: null,
            'details_json' => $this->buildDetailsJson($data),
            'note' => $data['note'] ?: null,
        ]);

        return redirect()->route('assets.index')
            ->with('success', '资产记录已创建。');
    }

    public function edit(Asset $asset): View
    {
        $this->authorizeAsset($asset);

        return view('assets.edit', [
            'asset' => $asset,
            'categories' => $this->categories,
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $this->authorizeAsset($asset);

        $data = $request->validated();
        $computedStatus = $this->computeStatus($data['due_date'] ?? null);

        $asset->update([
            'category' => $data['category'],
            'name' => $data['name'],
            'status' => $computedStatus,
            'due_date' => $data['due_date'] ?: null,
            'details_json' => $this->buildDetailsJson($data),
            'note' => $data['note'] ?: null,
        ]);

        return redirect()->route('assets.index')
            ->with('success', '资产记录已更新。');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $this->authorizeAsset($asset);

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', '资产记录已删除。');
    }

    protected function authorizeAsset(Asset $asset): void
    {
        abort_unless($asset->user_id === auth()->id(), 403);
    }

    protected function buildDetailsJson(array $data): array
    {
        return match ($data['category']) {
            '物理设备' => [
                'cpu_model' => $data['cpu_model'] ?? null,
                'gpu_model' => $data['gpu_model'] ?? null,
                'memory' => $data['memory'] ?? null,
                'storage_1' => $data['storage_1'] ?? null,
                'storage_2' => $data['storage_2'] ?? null,
                'storage_3' => $data['storage_3'] ?? null,
            ],
            '云服务器' => [
                'cpu_cores' => $data['cpu_cores'] ?? null,
                'memory_size' => $data['memory_size'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'operating_system' => $data['operating_system'] ?? null,
                'provider' => $data['provider'] ?? null,
            ],
            '域名' => [
                'domain_address' => $data['domain_address'] ?? null,
            ],
            default => [],
        };
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