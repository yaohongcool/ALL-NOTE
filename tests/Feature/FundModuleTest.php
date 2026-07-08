<?php

namespace Tests\Feature;

use App\Models\FundSkin;
use App\Models\FundSkinEarning;
use App\Models\User;
use App\Services\FundStatisticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FundModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(string $username = 'test-user'): User
    {
        return User::create([
            'username' => $username,
            'password' => Hash::make('Password@123'),
        ]);
    }

    public function test_fund_statistics_service_computes_skin_valuation(): void
    {
        $user = $this->createUser();

        $skin = $user->fundSkins()->create([
            'name' => 'Test Skin',
            'cost' => 100,
            'uu_price' => 150,
            'uu_fee_rate' => 0.02,
            'buff_price' => 140,
            'buff_fee_rate' => 0.025,
        ]);

        $service = new FundStatisticsService();
        $valuation = $service->skinValuation($skin);

        $uuProfit = 150 * (1 - 0.02) - 100;
        $buffProfit = 140 * (1 - 0.025) - 100;
        $expected = 100 + max($uuProfit, $buffProfit);

        $this->assertEqualsWithDelta($expected, $valuation, 0.001);
    }

    public function test_fund_statistics_service_computes_daily_profit(): void
    {
        $user = $this->createUser();

        $skin = $user->fundSkins()->create([
            'name' => 'Rental Skin',
            'cost' => 200,
            'daily_rental' => 4,
        ]);

        $service = new FundStatisticsService();
        $dailyProfit = $service->skinDailyProfit($skin);

        $expected = 4 * 0.8 * 0.5 * 0.99;

        $this->assertEqualsWithDelta($expected, $dailyProfit, 0.001);
    }

    public function test_fund_page_loads_successfully(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('funds.index'))
            ->assertOk();
    }

    public function test_skins_index_page_loads(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('funds.skins.index'))
            ->assertOk();
    }

    public function test_skin_create_and_store(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->withSession(['_token' => 'test-token'])->post(route('funds.skins.store'), [
            '_token' => 'test-token',
            'name' => 'AK-47 | Surface Hardened',
            'cost' => 99.99,
            'uu_price' => 150.00,
            'buff_price' => 145.00,
        ]);

        $response->assertRedirect(route('funds.skins.index'));

        $this->actingAs($user)->get(route('funds.skins.index'))
            ->assertOk()
            ->assertSee('AK-47 | Surface Hardened');
    }

    public function test_account_management_page_loads(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('funds.accounts.index'))
            ->assertOk();
    }

    public function test_budget_management_page_loads(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('funds.budgets.index'))
            ->assertOk();
    }

    public function test_monthly_records_page_loads(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('funds.monthlies.index'))
            ->assertOk();
    }

    public function test_historical_earnings_page_loads(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('funds.historical-earnings'))
            ->assertOk();
    }

    public function test_statistics_page_loads(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->get(route('funds.statistics'))
            ->assertOk();
    }

    public function test_chart_data_api_returns_json(): void
    {
        $user = $this->createUser();

        $user->fundMonthlies()->create([
            'month' => Carbon::create(2026, 1, 1),
            'income' => 5000,
            'note' => null,
        ]);
        $user->fundMonthlies()->create([
            'month' => Carbon::create(2026, 2, 1),
            'income' => 5500,
            'note' => null,
        ]);

        $this->actingAs($user)->get(route('funds.chart-data'))
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'month',
                    'income',
                    'cumulative',
                    'growth',
                ],
            ]);
    }

    public function test_cumulative_earnings_calculated_correctly(): void
    {
        $user = $this->createUser();

        $skin = $user->fundSkins()->create([
            'name' => 'Earning Skin',
            'cost' => 300,
        ]);

        $user->fundSkinEarnings()->create([
            'skin_id' => $skin->id,
            'month' => Carbon::create(2026, 1, 1),
            'revenue' => 100,
        ]);
        $user->fundSkinEarnings()->create([
            'skin_id' => $skin->id,
            'month' => Carbon::create(2026, 2, 1),
            'revenue' => 150,
        ]);

        $totalCumulativeEarnings = (float) $user->fundSkinEarnings()->sum('revenue');

        $this->assertEqualsWithDelta(250, $totalCumulativeEarnings, 0.01);
    }
}
