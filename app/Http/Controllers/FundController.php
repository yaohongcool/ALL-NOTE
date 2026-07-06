<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class FundController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('funds.index', [
            'accounts' => $user->fundAccounts()->orderBy('sort')->get(),
            'totalAssets' => $user->fundAccounts()->sum('balance'),
            'thisMonth' => $user->fundMonthlies()->where('month', now()->startOfMonth()->toDateString())->first(),
            'recentMonthlies' => $user->fundMonthlies()->orderByDesc('month')->limit(6)->get(),
            'budgetCount' => $user->fundBudgets()->count(),
            'skinCount' => $user->fundSkins()->count(),
        ]);
    }

    public function statistics(): View
    {
        return view('funds.statistics', [
            'years' => auth()->user()->fundMonthlies()->selectRaw('YEAR(month) as year')->distinct()->orderBy('year')->pluck('year'),
        ]);
    }
}
