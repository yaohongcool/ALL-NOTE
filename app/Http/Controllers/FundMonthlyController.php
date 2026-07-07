<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fund\StoreFundMonthlyRequest;
use App\Http\Requests\Fund\UpdateFundMonthlyRequest;
use App\Models\FundMonthly;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FundMonthlyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $monthlies = $user->fundMonthlies()
            ->orderByDesc('month')
            ->paginate(12);

        $growthData = [];
        foreach ($monthlies as $m) {
            $prev = $user->fundMonthlies()
                ->where('month', '<', $m->month)
                ->orderByDesc('month')
                ->first();
            $growthData[$m->id] = $prev
                ? $m->income - $prev->income
                : null;
        }

        return view('funds.monthlies.index', [
            'monthlies' => $monthlies,
            'growthData' => $growthData,
        ]);
    }

    public function create(): View
    {
        return view('funds.monthlies.create', [
            'monthly' => new FundMonthly(),
        ]);
    }

    public function store(StoreFundMonthlyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        auth()->user()->fundMonthlies()->create([
            'month' => $data['month'],
            'income' => $data['amount'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.monthlies.index')
            ->with('success', '月度记录已创建。');
    }

    public function edit(FundMonthly $monthly): View
    {
        $this->authorizeMonthly($monthly);

        return view('funds.monthlies.edit', [
            'monthly' => $monthly,
        ]);
    }

    public function update(UpdateFundMonthlyRequest $request, FundMonthly $monthly): RedirectResponse
    {
        $this->authorizeMonthly($monthly);

        $data = $request->validated();

        $monthly->update([
            'month' => $data['month'],
            'income' => $data['amount'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.monthlies.index')
            ->with('success', '月度记录已更新。');
    }

    public function destroy(FundMonthly $monthly): RedirectResponse
    {
        $this->authorizeMonthly($monthly);

        $monthly->delete();

        return redirect()->route('funds.monthlies.index')
            ->with('success', '月度记录已删除。');
    }

    protected function authorizeMonthly(FundMonthly $monthly): void
    {
        abort_unless($monthly->user_id === auth()->id(), 403);
    }
}
