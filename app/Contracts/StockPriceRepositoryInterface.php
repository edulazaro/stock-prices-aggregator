<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Models\StockPrice;

/**
 * Interface for the StockPriceRepository.
 *
 * Methods for managing the retrieval and storage of the StockPrice models.
 */
interface StockPriceRepositoryInterface
{
    /**
     * Retrieves all StockPrices
     *
     * @return Collection A collection of all StockPrices.
     */
    public function all(): Collection;

    /**
     * Retrieves a StockPrice by its symbol.
     *
     * @param string $symbol The stock symbol to search for.
     * @return StockPrice|null The found StockPrice, or null if not found.
     */
    public function getBySymbol(string $symbol): ?StockPrice;

    /**
     * Updates an existing StockPrice or creates a new one.
     *
     * @param array $attributes Conditions to match an existing record.
     * @param array $values Data to update or include in a new record.
     * @return StockPrice|null The updated or newly created StockPrice instance, or null on failure.
     */
    public function updateOrCreate(array $attributes, array $values): ?StockPrice;
}