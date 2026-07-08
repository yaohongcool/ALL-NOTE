<?php

namespace App\Services;

use App\Models\FundSkin;
use Illuminate\Support\Collection;

class FundStatisticsService
{
    const RENTAL_FEE_RATE = 0.8;
    const RENTAL_OCCUPANCY = 0.5;
    const RENTAL_PLATFORM_FEE = 0.99;
    const DAYS_PER_MONTH = 30.5;
    const DAYS_PER_YEAR = 365;

    public function skinBestProfit(FundSkin $skin): float
    {
        $uuProfit = ($skin->uu_price * (1 - $skin->uu_fee_rate)) - $skin->cost;
        $buffProfit = ($skin->buff_price * (1 - $skin->buff_fee_rate)) - $skin->cost;
        return max($uuProfit, $buffProfit);
    }

    public function skinValuation(FundSkin $skin): float
    {
        return $skin->cost + $this->skinBestProfit($skin);
    }

    public function skinDailyProfit(FundSkin $skin): float
    {
        return (float) ($skin->daily_rental ?? 0)
            * self::RENTAL_FEE_RATE
            * self::RENTAL_OCCUPANCY
            * self::RENTAL_PLATFORM_FEE;
    }

    public function skinMonthlyProfit(FundSkin $skin): float
    {
        return $this->skinDailyProfit($skin) * self::DAYS_PER_MONTH;
    }

    public function totalCost(Collection $skins): float
    {
        return (float) $skins->sum('cost');
    }

    public function totalValuation(Collection $skins): float
    {
        return (float) $skins->sum(fn (FundSkin $s) => $this->skinValuation($s));
    }

    public function totalBestProfit(Collection $skins): float
    {
        return (float) $skins->sum(fn (FundSkin $s) => $this->skinBestProfit($s));
    }

    public function totalDailyProfit(Collection $skins): float
    {
        return (float) $skins->sum(fn (FundSkin $s) => $this->skinDailyProfit($s));
    }

    public function totalMonthlyProfit(Collection $skins): float
    {
        return (float) $skins->sum(fn (FundSkin $s) => $this->skinMonthlyProfit($s));
    }

    public function avgMonthlyGrowth(Collection $monthlies): float
    {
        $sorted = $monthlies->sortBy('month')->values();
        $sum = 0;
        $count = 0;
        for ($i = 1; $i < $sorted->count(); $i++) {
            $sum += (float) $sorted[$i]->income - (float) $sorted[$i - 1]->income;
            $count++;
        }
        return $count > 0 ? $sum / $count : 0;
    }
}
