<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fund\StoreFundSkinRequest;
use App\Http\Requests\Fund\UpdateFundSkinRequest;
use App\Models\FundSkin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FundSkinController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $skins = $user->fundSkins()
            ->orderBy('name')
            ->paginate(10);

        $totalCost = $skins->sum('cost');
        $totalUuValuation = $skins->sum('uu_price');
        $totalBuffValuation = $skins->sum('buff_price');
        $totalProfit = $skins->sum(function (FundSkin $skin) {
            return ($skin->uu_price ?? 0) - $skin->cost;
        });

        $totalValuation = $skins->sum(function (FundSkin $skin) {
            $uuProfit = ($skin->uu_price * (1 - $skin->uu_fee_rate)) - $skin->cost;
            $buffProfit = ($skin->buff_price * (1 - $skin->buff_fee_rate)) - $skin->cost;
            $bestProfit = max($uuProfit, $buffProfit);
            return max($skin->cost, $skin->cost + $bestProfit);
        });

        return view('funds.skins.index', [
            'skins' => $skins,
            'totalCost' => $totalCost,
            'totalUuValuation' => $totalUuValuation,
            'totalBuffValuation' => $totalBuffValuation,
            'totalProfit' => $totalProfit,
            'totalValuation' => $totalValuation,
        ]);
    }

    public function create(): View
    {
        return view('funds.skins.create', [
            'skin' => new FundSkin(),
        ]);
    }

    public function store(StoreFundSkinRequest $request): RedirectResponse
    {
        $data = $request->validated();

        auth()->user()->fundSkins()->create([
            'name' => $data['name'],
            'cost' => $data['cost'],
            'uu_price' => $data['uu_price'] ?? null,
            'buff_price' => $data['buff_price'] ?? null,
            'daily_rental' => $data['daily_rental'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.skins.index')
            ->with('success', '饰品记录已创建。');
    }

    public function show(FundSkin $skin): View
    {
        $this->authorizeSkin($skin);

        $skin->load('rentals');

        return view('funds.skins.show', [
            'skin' => $skin,
            'rentals' => $skin->rentals,
        ]);
    }

    public function edit(FundSkin $skin): View
    {
        $this->authorizeSkin($skin);

        return view('funds.skins.edit', [
            'skin' => $skin,
        ]);
    }

    public function update(UpdateFundSkinRequest $request, FundSkin $skin): RedirectResponse
    {
        $this->authorizeSkin($skin);

        $data = $request->validated();

        $skin->update([
            'name' => $data['name'],
            'cost' => $data['cost'],
            'uu_price' => $data['uu_price'] ?? null,
            'buff_price' => $data['buff_price'] ?? null,
            'daily_rental' => $data['daily_rental'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.skins.index')
            ->with('success', '饰品记录已更新。');
    }

    public function destroy(FundSkin $skin): RedirectResponse
    {
        $this->authorizeSkin($skin);

        $skin->delete();

        return redirect()->route('funds.skins.index')
            ->with('success', '饰品记录已删除。');
    }

    protected function authorizeSkin(FundSkin $skin): void
    {
        abort_unless($skin->user_id === auth()->id(), 403);
    }
}
