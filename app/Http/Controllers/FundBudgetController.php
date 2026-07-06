<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fund\StoreFundBudgetRequest;
use App\Http\Requests\Fund\UpdateFundBudgetRequest;
use App\Models\FundBudget;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FundBudgetController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $budgets = $user->fundBudgets()
            ->orderBy('name')
            ->paginate(20);

        $allBudgets = $user->fundBudgets()->get();

        return view('funds.budgets.index', [
            'budgets' => $budgets,
            'monthlyExpenseTotal' => $allBudgets->where('type', 'expense')->sum('monthly_amount'),
            'monthlyIncomeTotal' => $allBudgets->where('type', 'income')->sum('monthly_amount'),
        ]);
    }

    public function create(): View
    {
        return view('funds.budgets.create', [
            'budget' => new FundBudget(),
        ]);
    }

    public function store(StoreFundBudgetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $monthly = $data['monthly_amount'] ?? null;
        $annual = $data['annual_amount'] ?? null;

        if (filled($monthly) && blank($annual)) {
            $annual = $monthly * 12;
        }
        if (filled($annual) && blank($monthly)) {
            $monthly = $annual / 12;
        }

        auth()->user()->fundBudgets()->create([
            'name' => $data['name'],
            'type' => $data['type'],
            'monthly_amount' => $monthly,
            'annual_amount' => $annual,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.budgets.index')
            ->with('success', '预算记录已创建。');
    }

    public function edit(FundBudget $budget): View
    {
        $this->authorizeBudget($budget);

        return view('funds.budgets.edit', [
            'budget' => $budget,
        ]);
    }

    public function update(UpdateFundBudgetRequest $request, FundBudget $budget): RedirectResponse
    {
        $this->authorizeBudget($budget);

        $data = $request->validated();

        $monthly = $data['monthly_amount'] ?? null;
        $annual = $data['annual_amount'] ?? null;

        if (filled($monthly) && blank($annual)) {
            $annual = $monthly * 12;
        }
        if (filled($annual) && blank($monthly)) {
            $monthly = $annual / 12;
        }

        $budget->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'monthly_amount' => $monthly,
            'annual_amount' => $annual,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.budgets.index')
            ->with('success', '预算记录已更新。');
    }

    public function destroy(FundBudget $budget): RedirectResponse
    {
        $this->authorizeBudget($budget);

        $budget->delete();

        return redirect()->route('funds.budgets.index')
            ->with('success', '预算记录已删除。');
    }

    protected function authorizeBudget(FundBudget $budget): void
    {
        abort_unless($budget->user_id === auth()->id(), 403);
    }
}
