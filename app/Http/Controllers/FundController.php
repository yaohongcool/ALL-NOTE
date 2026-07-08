<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class FundController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $skins = $user->fundSkins()->get();
        $totalSkinCost = $skins->sum('cost');
        $totalSkinValuation = $skins->sum(function ($s) {
            $uuProfit = ($s->uu_price * (1 - $s->uu_fee_rate)) - $s->cost;
            $buffProfit = ($s->buff_price * (1 - $s->buff_fee_rate)) - $s->cost;
            return max($s->cost, $s->cost + max($uuProfit, $buffProfit));
        });

        // 环比增长平均值（近12个月）
        $recentMonthlies = $user->fundMonthlies()
            ->orderByDesc('month')
            ->limit(13)
            ->get(['id', 'income', 'month'])
            ->sortBy('month')
            ->values();

        $growthSum = 0;
        $growthCount = 0;
        for ($i = 1; $i < $recentMonthlies->count(); $i++) {
            $prev = (float) $recentMonthlies[$i - 1]->income;
            $curr = (float) $recentMonthlies[$i]->income;
            $growthSum += $curr - $prev;
            $growthCount++;
        }
        $avgGrowth = $growthCount > 0 ? $growthSum / $growthCount : 0;

        // 日均收益 / 月均收益（一览表总和）
        $totalDailyProfit = 0;
        $totalMonthlyProfit = 0;
        foreach ($skins as $s) {
            $rent = (float) ($s->daily_rental ?? 0);
            $daily = $rent * 0.8 * 0.5 * 0.99;
            $monthly = $daily * 30.5;
            $totalDailyProfit += $daily;
            $totalMonthlyProfit += $monthly;
        }

        $totalCumulativeEarnings = $user->fundSkinEarnings()->sum('revenue');

        return view('funds.index', [
            'accounts' => $user->fundAccounts()->orderByDesc('balance')->get(),
            'totalAssets' => $user->fundAccounts()->sum('balance') + $totalSkinValuation,
            'skins' => $skins,
            'totalSkinCost' => $totalSkinCost,
            'totalSkinValuation' => $totalSkinValuation,
            'avgGrowth' => $avgGrowth,
            'totalDailyProfit' => $totalDailyProfit,
            'totalMonthlyProfit' => $totalMonthlyProfit,
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
            ->selectRaw('YEAR(month) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('funds.statistics', compact('years'));
    }
}
