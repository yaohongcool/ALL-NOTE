<?php

namespace App\Http\Controllers;

use App\Services\FundStatisticsService;
use Illuminate\Contracts\View\View;

class FundController extends Controller
{
    public function __construct(
        protected FundStatisticsService $stats
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        $skins = $user->fundSkins()->get();
        $totalSkinValuation = $this->stats->totalValuation($skins);

        // 环比增长平均值（近12个月）
        $recentMonthlies = $user->fundMonthlies()
            ->orderByDesc('month')
            ->limit(13)
            ->get(['id', 'income', 'month']);

        $totalCumulativeEarnings = $user->fundSkinEarnings()->sum('revenue');

        return view('funds.index', [
            'accounts' => $user->fundAccounts()->orderByDesc('balance')->get(),
            'totalAssets' => $user->fundAccounts()->sum('balance') + $totalSkinValuation,
            'skins' => $skins,
            'totalSkinValuation' => $totalSkinValuation,
            'avgGrowth' => $this->stats->avgMonthlyGrowth($recentMonthlies),
            'totalDailyProfit' => $this->stats->totalDailyProfit($skins),
            'totalMonthlyProfit' => $this->stats->totalMonthlyProfit($skins),
            'totalCumulativeEarnings' => $totalCumulativeEarnings,
        ]);
    }

    public function historicalEarnings(): View
    {
        return view('funds.historical_earnings');
    }

    public function statistics(): View
    {
        $user = auth()->user();
        $years = $user->fundMonthlies()
            ->orderBy('month')
            ->pluck('month')
            ->map(fn ($m) => $m instanceof \Carbon\Carbon ? $m->year : (int) date('Y', strtotime($m)))
            ->unique()
            ->sortDesc()
            ->values();

        return view('funds.statistics', compact('years'));
    }
}
