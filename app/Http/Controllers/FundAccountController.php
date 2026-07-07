<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fund\StoreFundAccountRequest;
use App\Http\Requests\Fund\UpdateFundAccountRequest;
use App\Models\FundAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FundAccountController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $accounts = $user->fundAccounts()
            ->select(['id', 'user_id', 'name', 'balance', 'note', 'created_at', 'updated_at'])
            ->orderByDesc('balance')
            ->paginate(10);

        return view('funds.accounts.index', [
            'accounts' => $accounts,
        ]);
    }

    public function create(): View
    {
        return view('funds.accounts.create', [
            'account' => new FundAccount(),
        ]);
    }

    public function store(StoreFundAccountRequest $request): RedirectResponse
    {
        $data = $request->validated();

        auth()->user()->fundAccounts()->create([
            'name' => $data['name'],
            'type' => $data['type'] ?? 'other',
            'balance' => $data['balance'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.accounts.index')
            ->with('success', '资金账户已创建。');
    }

    public function edit(FundAccount $account): View
    {
        $this->authorizeAccount($account);

        return view('funds.accounts.edit', [
            'account' => $account,
        ]);
    }

    public function update(UpdateFundAccountRequest $request, FundAccount $account): RedirectResponse
    {
        $this->authorizeAccount($account);

        $data = $request->validated();

        $account->update([
            'name' => $data['name'],
            'type' => $data['type'] ?? 'other',
            'balance' => $data['balance'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.accounts.index')
            ->with('success', '资金账户已更新。');
    }

    public function destroy(FundAccount $account): RedirectResponse
    {
        $this->authorizeAccount($account);

        $account->delete();

        return redirect()->route('funds.accounts.index')
            ->with('success', '资金账户已删除。');
    }

    protected function authorizeAccount(FundAccount $account): void
    {
        abort_unless($account->user_id === auth()->id(), 403);
    }
}
