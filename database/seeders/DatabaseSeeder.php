<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\StockPrice;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $stockSymbols = config('services.alphavantage.stock_symbols');

        foreach($stockSymbols as $stockSymbol) {
            StockPrice::factory()->create([
                'symbol' => $stockSymbol,
            ]);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
