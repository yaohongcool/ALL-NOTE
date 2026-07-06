<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundChartController extends Controller
{
    public function chartData(Request $request): JsonResponse
    {
        $year = (int) ($request->query('year', now()->year));

        $records = auth()->user()->fundMonthlies()
            ->whereYear('month', $year)
            ->orderBy('month')
            ->get(['month', 'income', 'expense', 'savings_target', 'savings_actual']);

        return response()->json($records);
    }
}
