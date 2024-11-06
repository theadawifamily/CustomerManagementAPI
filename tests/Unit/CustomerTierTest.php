<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * @group tier
 */
class CustomerTierTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_tier_is_silver_when_annual_spend_is_null()
    {
        $customer = Customer::factory()->create([
            'annualSpend' => null,
        ]);

        $this->assertEquals(Customer::TIER_SILVER, $customer->tier);
    }

    public function test_customer_tier_is_silver_when_annual_spend_is_less_than_1000()
    {
        $customer = Customer::factory()->create([
            'annualSpend' => 500.00,
        ]);

        $this->assertEquals(Customer::TIER_SILVER, $customer->tier);
    }

    public function test_customer_tier_is_gold_when_annual_spend_is_between_1000_and_10000_and_purchase_within_last_12_months()
    {
        $customer = Customer::factory()->create([
            'annualSpend' => 5000.00,
            'lastPurchaseDate' => Carbon::now()->subMonths(6),
        ]);

        $this->assertEquals(Customer::TIER_GOLD, $customer->tier);
    }

    public function test_customer_tier_is_platinum_when_annual_spend_is_10000_or_more_and_purchase_within_last_6_months()
    {
        $customer = Customer::factory()->create([
            'annualSpend' => 15000.00,
            'lastPurchaseDate' => Carbon::now()->subMonths(3),
        ]);

        $this->assertEquals(Customer::TIER_PLATINUM, $customer->tier);
    }

    public function test_customer_tier_is_silver_when_annual_spend_is_above_1000_but_purchase_older_than_12_months()
    {
        $customer = Customer::factory()->create([
            'annualSpend' => 5000.00,
            'lastPurchaseDate' => Carbon::now()->subYears(2),
        ]);

        $this->assertEquals(Customer::TIER_SILVER, $customer->tier);
    }
}
