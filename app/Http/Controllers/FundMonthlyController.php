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

        return view('funds.monthlies.index', [
            'monthlies' => $monthlies,
        ]);
    }

    public function create(): View
    {
        $user = auth()->user();

        $previous = $user->fundMonthlies()
            ->orderByDesc('month')
            ->first();

        $defaultTarget = $previous ? $previous->savings_target + 2500 : 0;

        return view('funds.monthlies.create', [
            'monthly' => new FundMonthly([
                'savings_target' => $defaultTarget,
            ]),
        ]);
    }

    public function store(StoreFundMonthlyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! isset($data['savings_target'])) {
            $previous = auth()->user()->fundMonthlies()
                ->orderByDesc('month')
                ->first();
            $data['savings_target'] = $previous ? $previous->savings_target + 2500 : 0;
        }

        auth()->user()->fundMonthlies()->create([
            'month' => $data['month'],
            'income' => $data['income'],
            'expense' => $data['expense'],
            'savings_target' => $data['savings_target'],
            'savings_actual' => $data['savings_actual'] ?? null,
            'savings_status' => $data['savings_status'] ?? 'uncompleted',
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
            'income' => $data['income'],
            'expense' => $data['expense'],
            'savings_target' => $data['savings_target'] ?? $monthly->savings_target,
            'savings_actual' => $data['savings_actual'] ?? null,
            'savings_status' => $data['savings_status'] ?? 'uncompleted',
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
