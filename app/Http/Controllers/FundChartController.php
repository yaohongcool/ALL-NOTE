<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundChartController extends Controller
{
    public function chartData(Request $request): JsonResponse
    {
        $year = (int) ($request->query('year', now()->year));

        $allRecords = auth()->user()->fundMonthlies()
            ->orderBy('month')
            ->get(['month', 'income', 'expense']);

        $prevIncome = null;
        $result = [];
        foreach ($allRecords as $r) {
            $income = (float) ($r->income ?? 0);
            $growth = $prevIncome !== null ? $income - $prevIncome : null;
            $prevIncome = $income;

            $rYear = \Carbon\Carbon::parse($r->month)->year;
            if ($rYear != $year) {
                continue;
            }

            $result[] = [
                'month' => $r->month instanceof \Carbon\Carbon
                    ? $r->month->format('Y-m-d')
                    : $r->month,
                'income' => $income,
                'cumulative' => $income,
                'growth' => $growth,
            ];
        }

        return response()->json($result);
    }
}
