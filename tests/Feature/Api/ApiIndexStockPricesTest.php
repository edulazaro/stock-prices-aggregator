<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\StockPrice;

class ApiIndexStockPricesTest extends TestCase
{
    use RefreshDatabase;

    private $stockSymbols;

    public function setUp(): void
    {
        parent::setUp();

        $this->stockSymbols = config('services.alphavantage.stock_symbols');

        foreach($this->stockSymbols as $stockSymbol) {
            StockPrice::factory()->create([
                'symbol' => $stockSymbol,
            ]);
        }
    }

    /** @test */
    public function test_retrieved_successfully(): void
    {
        $response = $this->get('/api/stock-prices');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_has_valid_data(): void
    {
        $response = $this->get('/api/stock-prices');

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ])->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
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
            ],
        ])
        ->assertJsonCount(count($this->stockSymbols), 'data');
    }
}
