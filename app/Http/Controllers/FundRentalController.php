<?php

namespace App\Http\Controllers;

use App\Http\Requests\Fund\StoreFundRentalRequest;
use App\Http\Requests\Fund\UpdateFundRentalRequest;
use App\Models\FundRental;
use App\Models\FundSkin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FundRentalController extends Controller
{
    public function index(FundSkin $skin): View
    {
        $this->authorizeSkin($skin);

        $rentals = $skin->rentals()->orderByDesc('created_at')->paginate(10);

        return view('funds.rentals.index', [
            'skin' => $skin,
            'rentals' => $rentals,
        ]);
    }

    public function create(FundSkin $skin): View
    {
        $this->authorizeSkin($skin);

        return view('funds.rentals.create', [
            'skin' => $skin,
            'rental' => new FundRental([
                'skin_id' => $skin->id,
            ]),
        ]);
    }

    public function store(StoreFundRentalRequest $request, FundSkin $skin): RedirectResponse
    {
        $this->authorizeSkin($skin);

        $data = $request->validated();

        $revenue = $this->computeRevenue(
            (float) $data['rate'],
            (float) ($data['discount'] ?? 0.8),
            (int) $data['lease_days']
        );

        auth()->user()->fundRentals()->create([
            'skin_id' => $skin->id,
            'type' => $data['type'],
            'rate' => $data['rate'],
            'discount' => $data['discount'] ?? 0.8,
            'lease_days' => $data['lease_days'],
            'offhand_days' => $data['offhand_days'] ?? 8,
            'fee_rate' => $data['fee_rate'] ?? 0.99,
            'revenue' => $revenue,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.skins.index')
            ->with('success', '租赁记录已创建。');
    }

    public function edit(FundSkin $skin, FundRental $rental): View
    {
        $this->authorizeSkin($skin);

        return view('funds.rentals.edit', [
            'skin' => $skin,
            'rental' => $rental,
        ]);
    }

    public function update(UpdateFundRentalRequest $request, FundSkin $skin, FundRental $rental): RedirectResponse
    {
        $this->authorizeSkin($skin);

        $data = $request->validated();

        $revenue = $this->computeRevenue(
            (float) $data['rate'],
            (float) ($data['discount'] ?? 0.8),
            (int) $data['lease_days']
        );

        $rental->update([
            'type' => $data['type'],
            'rate' => $data['rate'],
            'discount' => $data['discount'] ?? 0.8,
            'lease_days' => $data['lease_days'],
            'offhand_days' => $data['offhand_days'] ?? 8,
            'fee_rate' => $data['fee_rate'] ?? 0.99,
            'revenue' => $revenue,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('funds.skins.index')
            ->with('success', '租赁记录已更新。');
    }

    public function destroy(FundSkin $skin, FundRental $rental): RedirectResponse
    {
        $this->authorizeSkin($skin);

        $rental->delete();

        return redirect()->route('funds.skins.index')
            ->with('success', '租赁记录已删除。');
    }

    protected function authorizeSkin(FundSkin $skin): void
    {
        abort_unless($skin->user_id === auth()->id(), 403);
    }

    protected function computeRevenue(float $rate, float $discount, int $leaseDays): float
    {
        return round($rate * $discount * $leaseDays, 2);
    }
}
