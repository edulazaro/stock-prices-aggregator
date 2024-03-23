<?php

namespace App\Observers;

use App\Models\StockPrice;
use Illuminate\Support\Facades\Cache;

class StockPriceObserver
{
    /**
     * Handle the StockPrice "created" event.
     * 
     * @param  StockPrice  $stockPrice  The StockPrice created
     * @return void
     */
    public function created(StockPrice $stockPrice): void
    {
        Cache::put('stock_price.' . $stockPrice->symbol, $stockPrice, 60);
    }

    /**
     * Handle the StockPrice "updated" event.
     * 
     * @param  StockPrice  $stockPrice  The updated StockPrice
     * @return void
     */
    public function updated(StockPrice $stockPrice): void
    {
        Cache::put('stock_price.' . $stockPrice->symbol, $stockPrice, 60);
    }
}
