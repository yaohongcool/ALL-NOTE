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

        return view('funds.index', [
            'accounts' => $user->fundAccounts()->orderBy('sort')->get(),
            'totalAssets' => $user->fundAccounts()->sum('balance') + $totalSkinValuation,
            'skins' => $skins,
            'totalSkinCost' => $totalSkinCost,
            'totalSkinValuation' => $totalSkinValuation,
            'avgGrowth' => $avgGrowth,
            'totalDailyProfit' => $totalDailyProfit,
            'totalMonthlyProfit' => $totalMonthlyProfit,
        ]);
    }

    public function historicalEarnings(): View
    {
        $user = auth()->user();
        $skins = $user->fundSkins()->orderBy('name')->get();

        $earnings = $user->fundSkinEarnings()
            ->with('skin')
            ->orderBy('month')
            ->get()
            ->groupBy(fn ($e) => $e->month instanceof \Carbon\Carbon
                ? $e->month->format('Y-m')
                : date('Y-m', strtotime($e->month)));

        $earningsByMonth = [];
        foreach ($earnings as $ym => $items) {
            $row = ['ym' => $ym, 'items' => []];
            foreach ($items as $e) {
                $row['items'][$e->skin_id] = (float) $e->revenue;
            }
            $earningsByMonth[] = $row;
        }

        $totalsBySkin = [];
        foreach ($skins as $skin) {
            $totalsBySkin[$skin->id] = 0;
            foreach ($earningsByMonth as $row) {
                $totalsBySkin[$skin->id] += $row['items'][$skin->id] ?? 0;
            }
        }

        return view('funds.historical_earnings', compact('skins', 'earningsByMonth', 'totalsBySkin'));
    }

    public function statistics(): View
    {
        $user = auth()->user();
        $years = $user->fundMonthlies()
            ->selectRaw('YEAR(month) as year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');

        return view('funds.statistics', compact('years'));
    }
}
