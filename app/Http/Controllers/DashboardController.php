<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Document;
use App\Services\FundStatisticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class DashboardController extends Controller
{
    public function __construct(
        protected FundStatisticsService $stats
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        $assetsOverview = $user->assets()
            ->oldest('updated_at')
            ->limit(10)
            ->get();

        $assetReminders = $user->assets()
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn (Asset $asset) => $this->toReminderArray($asset, 'asset'));

        $documentReminders = $user->documents()
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn (Document $document) => $this->toReminderArray($document, 'document'));

        $reminders = $assetReminders
            ->concat($documentReminders)
            ->sortBy(fn ($item) => optional($item['due_date'])->timestamp ?? PHP_INT_MAX)
            ->values();

        // 资金统计
        $skins = $user->fundSkins()->get();
        $totalSkinValuation = $this->stats->totalValuation($skins);

        $recentMonthlies = $user->fundMonthlies()
            ->orderByDesc('month')
            ->limit(13)
            ->get(['id', 'income', 'month']);

        return view('dashboard', [
            'stats' => [
                'passwords_count' => $user->passwords()->count(),
                'assets_count' => $user->assets()->count(),
                'documents_count' => $user->documents()->count(),
                'events_count' => $user->events()->count(),
            ],
            'fundTotalAssets' => $user->fundAccounts()->sum('balance') + $totalSkinValuation,
            'fundAvgGrowth' => $this->stats->avgMonthlyGrowth($recentMonthlies),
            'fundDailyProfit' => $this->stats->totalDailyProfit($skins),
            'fundMonthlyProfit' => $this->stats->totalMonthlyProfit($skins),
            'assetsOverview' => $assetsOverview,
            'reminders' => $reminders,
        ]);
    }

    private function toReminderArray(Model $model, string $type): array
    {
        return [
            'type' => $type,
            'title' => $type === 'asset' ? $model->name : $model->name,
            'category' => $model->category,
            'status' => $model->computed_status,
            'days_until_due_label' => $model->days_until_due_label,
            'due_date' => $model->due_date,
            'note' => $model->note,
        ];
    }
}
