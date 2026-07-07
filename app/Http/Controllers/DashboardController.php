<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Document;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class DashboardController extends Controller
{
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
        $totalSkinValuation = $skins->sum(function ($s) {
            $uuProfit = ($s->uu_price * (1 - $s->uu_fee_rate)) - $s->cost;
            $buffProfit = ($s->buff_price * (1 - $s->buff_fee_rate)) - $s->cost;
            return $s->cost + max($uuProfit, $buffProfit);
        });

        $recentMonthlies = $user->fundMonthlies()
            ->orderByDesc('month')
            ->limit(13)
            ->get(['id', 'income', 'month'])
            ->sortBy('month')
            ->values();

        $growthSum = 0;
        $growthCount = 0;
        for ($i = 1; $i < $recentMonthlies->count(); $i++) {
            $growthSum += (float) $recentMonthlies[$i]->income - (float) $recentMonthlies[$i - 1]->income;
            $growthCount++;
        }

        $totalDailyProfit = 0;
        $totalMonthlyProfit = 0;
        foreach ($skins as $s) {
            $daily = (float) ($s->daily_rental ?? 0) * 0.8 * 0.5 * 0.99;
            $totalDailyProfit += $daily;
            $totalMonthlyProfit += $daily * 30.5;
        }

        return view('dashboard', [
            'stats' => [
                'passwords_count' => $user->passwords()->count(),
                'assets_count' => $user->assets()->count(),
                'documents_count' => $user->documents()->count(),
                'events_count' => $user->events()->count(),
            ],
            'fundTotalAssets' => $user->fundAccounts()->sum('balance') + $totalSkinValuation,
            'fundAvgGrowth' => $growthCount > 0 ? $growthSum / $growthCount : 0,
            'fundDailyProfit' => $totalDailyProfit,
            'fundMonthlyProfit' => $totalMonthlyProfit,
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
