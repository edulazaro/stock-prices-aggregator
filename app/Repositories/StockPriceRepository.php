<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

use App\Models\StockPrice;

/**
 * Manages the retrieval and persistence of StockPrice models.
 *
 * Provides methods to fetch all stock prices or by symbol, and to update or create them.
 * 
 * @author Edu Lazaro
 */
class StockPriceRepository
{
    /**
     * Retrieves all StockPrices, caching the result.
     *
     * @return Collection Cached collection of all StockPrices.
     */
    public function all(): Collection
    {
        $stockPrices = Cache::remember('stock_prices', 60, function () {
            return StockPrice::all();
        });

        return $stockPrices;
    }

    /**
     * Retrieves a StockPrice by its symbol, caching the result.
     *
     * @param string $symbol The stock symbol to search for.
     * @return StockPrice|null The found StockPrice, or null if not found.
     */
    public function getBySymbol(string $symbol): ?StockPrice
    {
        $stockPrice = Cache::remember('stock_price.' . $symbol, 60, function () use ($symbol) {
            return StockPrice::where('symbol', $symbol)->first();
        });

        return $stockPrice;
    }

    /**
     * Updates an existing StockPrice or creates a new one based on provided attributes and values.
     *
     * @param array $attributes Conditions to match an existing record.
     * @param array $values Data to update or include in a new record.
     * @return StockPrice|null The updated or newly created StockPrice instance, or null on failure.
     */
    public function updateOrCreate(array $attributes, array $values): ?StockPrice
    {
        return StockPrice::updateOrCreate($attributes, $values);
    }
}
