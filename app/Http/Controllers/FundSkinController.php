<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fund\StoreFundSkinRequest;
use App\Http\Requests\Fund\UpdateFundSkinRequest;
use App\Models\FundSkin;
use App\Services\FundStatisticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FundSkinController extends Controller
{
    public function __construct(
        protected FundStatisticsService $stats
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        $skinsCollection = $user->fundSkins()->get();
        $skins = $user->fundSkins()
            ->orderBy('name')
            ->paginate(10);

        // 把集合传给 paginator 以便计算合计
        $allSkins = $skinsCollection;

        return view('funds.skins.index', [
            'skins' => $skins,
            'totalCost' => $this->stats->totalCost($allSkins),
            'totalProfit' => $this->stats->totalBestProfit($allSkins),
            'totalValuation' => $this->stats->totalValuation($allSkins),
            'totalDailyProfit' => $this->stats->totalDailyProfit($allSkins),
            'totalMonthlyProfit' => $this->stats->totalMonthlyProfit($allSkins),
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
            'purchased_at' => $data['purchased_at'] ?? null,
            'cost' => $data['cost'],
            'uu_price' => $data['uu_price'] ?? null,
            'uu_fee_rate' => $data['uu_fee_rate'] ?? 0.02,
            'buff_price' => $data['buff_price'] ?? null,
            'buff_fee_rate' => $data['buff_fee_rate'] ?? 0.025,
            'daily_rental' => $data['daily_rental'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.skins.index')
            ->with('success', '虚拟资产记录已创建。');
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
            'purchased_at' => $data['purchased_at'] ?? null,
            'cost' => $data['cost'],
            'uu_price' => $data['uu_price'] ?? null,
            'uu_fee_rate' => $data['uu_fee_rate'] ?? $skin->uu_fee_rate,
            'buff_price' => $data['buff_price'] ?? null,
            'buff_fee_rate' => $data['buff_fee_rate'] ?? $skin->buff_fee_rate,
            'daily_rental' => $data['daily_rental'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.skins.index')
            ->with('success', '虚拟资产记录已更新。');
    }

    public function destroy(FundSkin $skin): RedirectResponse
    {
        $this->authorizeSkin($skin);

        $skin->delete();

        return redirect()->route('funds.skins.index')
            ->with('success', '虚拟资产记录已删除。');
    }

    protected function authorizeSkin(FundSkin $skin): void
    {
        abort_unless($skin->user_id === auth()->id(), 403);
    }
}
