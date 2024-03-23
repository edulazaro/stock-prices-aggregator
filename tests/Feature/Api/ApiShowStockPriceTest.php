<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\StockPrice;

class ApiShowStockPriceTest extends TestCase
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
    }

    /**
     * A basic feature test example.
     */
    public function test_nonexistent_stock_retrieval_fails(): void
    {
        $response = $this->get('/api/stock-prices/HEY');

        $response->assertStatus(404)->assertJson([
            'success' => false,
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_existin_stock_retrieved_successfully(): void
    {
        $response = $this->get('/api/stock-prices/IBM');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_has_valid_data(): void
    {
        $response = $this->get('/api/stock-prices/IBM');

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ])->assertJsonStructure([
            'success',
            'data' => [
                'symbol',
                'price',
                'volume',
                'open',
                'high',
                'low',
                'previous_close',
                'created_at',
                'updated_at',
                'percentage_change',
            ],
        ])
        ->assertJsonPath('data.symbol', 'IBM');
    }
}
