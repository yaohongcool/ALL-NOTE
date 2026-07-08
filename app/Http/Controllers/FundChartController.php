<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundChartController extends Controller
{
    public function chartData(Request $request): JsonResponse
    {
        $yearParam = $request->query('year', 'all');

        $allRecords = auth()->user()->fundMonthlies()
            ->orderBy('month')
            ->get(['month', 'income', 'expense']);

        // If "all", take last 12 months; otherwise filter by year
        if ($yearParam === 'all') {
            $records = $allRecords->take(-12);
        } else {
            $year = (int) $yearParam;
            $records = $allRecords->filter(fn ($r) => \Carbon\Carbon::parse($r->month)->year === $year)->values();
        }

        $prevIncome = null;
        $result = [];
        foreach ($records as $r) {
            $income = (float) ($r->income ?? 0);
            $growth = $prevIncome !== null ? $income - $prevIncome : null;
            $prevIncome = $income;

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
