<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fund\StoreFundSkinEarningRequest;
use App\Http\Requests\Fund\UpdateFundSkinEarningRequest;
use App\Models\FundSkinEarning;
use App\Models\FundSkin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FundSkinEarningController extends Controller
{
    public function index(FundSkin $skin): View
    {
        $this->authorize($skin);

        $earnings = $skin->earnings()->orderByDesc('month')->paginate(12);

        return view('funds.skin_earnings.index', compact('skin', 'earnings'));
    }

    public function create(FundSkin $skin): View
    {
        $this->authorize($skin);

        return view('funds.skin_earnings.create', [
            'skin' => $skin,
            'earning' => new FundSkinEarning(['skin_id' => $skin->id, 'month' => now()->startOfMonth()->toDateString()]),
        ]);
    }

    public function store(FundSkin $skin, StoreFundSkinEarningRequest $request): RedirectResponse
    {
        $this->authorize($skin);

        auth()->user()->fundSkinEarnings()->create([
            'skin_id' => $skin->id,
            'month' => $request->validated()['month'],
            'revenue' => $request->validated()['revenue'],
            'note' => $request->validated()['note'] ?? null,
        ]);

        return redirect()->route('funds.skins.index')->with('success', '收益记录已创建。');
    }

    public function edit(FundSkin $skin, FundSkinEarning $earning): View
    {
        $this->authorize($skin);

        return view('funds.skin_earnings.edit', compact('skin', 'earning'));
    }

    public function update(FundSkin $skin, FundSkinEarning $earning, UpdateFundSkinEarningRequest $request): RedirectResponse
    {
        $this->authorize($skin);

        $earning->update($request->validated());

        return redirect()->route('funds.skins.index')->with('success', '收益记录已更新。');
    }

    public function destroy(FundSkin $skin, FundSkinEarning $earning): RedirectResponse
    {
        $this->authorize($skin);

        $earning->delete();

        return redirect()->route('funds.skins.index')->with('success', '收益记录已删除。');
    }

    protected function authorize(FundSkin $skin): void
    {
        abort_unless($skin->user_id === auth()->id(), 403);
    }
}
