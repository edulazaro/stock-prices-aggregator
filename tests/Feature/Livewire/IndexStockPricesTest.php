<?php

namespace Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

use App\Models\User;
use App\Models\StockPrice;

class IndexStockPricesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $stockSymbols = config('services.alphavantage.stock_symbols');

        foreach ($stockSymbols as $stockSymbol) {
            StockPrice::factory()->create([
                'symbol' => $stockSymbol,
            ]);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function component_renders_successfully()
    {
        Volt::test('index-stock-prices')->assertSeeVolt('index-stock-prices');
    }
}
