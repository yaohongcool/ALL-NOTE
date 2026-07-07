<?php

namespace App\Http\Controllers;

use App\Models\FundEarningPeriod;
use App\Models\FundSkin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundEarningPeriodController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $skins = $user->fundSkins()->orderBy('name')->get(['id', 'name']);

        $periods = $user->fundEarningPeriods()
            ->with(['skinEarnings' => fn ($q) => $q->select(['id', 'period_id', 'skin_id', 'revenue', 'original_amount'])])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'skins' => $skins,
            'periods' => $periods->map(fn ($p) => [
                'id' => $p->id,
                'label' => $p->label,
                'sort_order' => $p->sort_order,
                'amounts' => $p->skinEarnings->pluck('revenue', 'skin_id')->map(fn ($v) => (float) ($v ?? 0)),
                'original_amounts' => $p->skinEarnings->pluck('original_amount', 'skin_id')->map(fn ($v) => (float) ($v ?? 0)),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $nextNum = $user->fundEarningPeriods()->count() + 1;

        $period = $user->fundEarningPeriods()->create([
            'label' => '第' . $nextNum . '期',
            'sort_order' => $nextNum,
        ]);

        return response()->json(['id' => $period->id, 'label' => $period->label, 'sort_order' => $period->sort_order]);
    }

    public function update(Request $request, FundEarningPeriod $period): JsonResponse
    {
        abort_unless($period->user_id === auth()->id(), 403);

        $request->validate([
            'amounts' => ['required', 'array'],
            'amounts.*' => ['numeric', 'min:0'],
        ]);

        $user = auth()->user();
        $skins = $user->fundSkins()->get(['id', 'name']);

        $period->skinEarnings()->delete();

        foreach ($skins as $skin) {
            $originalAmount = (float) ($request->input('amounts.' . $skin->id) ?? 0);
            if ($originalAmount != 0) {
                $revenue = round($originalAmount * 0.8, 2);
                $period->skinEarnings()->create([
                    'skin_id' => $skin->id,
                    'user_id' => $user->id,
                    'month' => now(),
                    'revenue' => $revenue,
                    'original_amount' => $originalAmount,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function destroy(FundEarningPeriod $period): JsonResponse
    {
        abort_unless($period->user_id === auth()->id(), 403);
        $period->delete();
        return response()->json(['status' => 'ok']);
    }
}
