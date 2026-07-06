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

        return view('funds.index', [
            'accounts' => $user->fundAccounts()->orderBy('sort')->get(),
            'totalAssets' => $user->fundAccounts()->sum('balance') + $totalSkinValuation,
            'thisMonth' => $user->fundMonthlies()->where('month', now()->startOfMonth()->toDateString())->first(),
            'recentMonthlies' => $user->fundMonthlies()->orderByDesc('month')->limit(6)->get(),
            'budgetCount' => $user->fundBudgets()->count(),
            'skins' => $skins,
            'totalSkinCost' => $totalSkinCost,
            'totalSkinValuation' => $totalSkinValuation,
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
        return view('funds.statistics', [
            'years' => auth()->user()->fundMonthlies()->selectRaw('YEAR(month) as year')->distinct()->orderBy('year')->pluck('year'),
        ]);
    }
}
