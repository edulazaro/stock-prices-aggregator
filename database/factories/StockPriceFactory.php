<?php

namespace Database\Factories;

use App\Models\StockPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockPriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockPrice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $price = round(fake()->randomFloat(2, $min = 1, $max = 1000), 2);
        $openPrice = round($price * fake()->randomFloat(2, 0.95, 1.05), 2);

        return [
            'price' => $price,
            'volume' => fake()->numberBetween(100, 1000000),
            'open' => round($price * fake()->randomFloat(2, 0.95, 1.05), 2),
            'high' => max($price, $openPrice) * fake()->randomFloat(2, 1.01, 1.1),
            'low' => min($price, $openPrice) * fake()->randomFloat(2, 0.9, 0.99),
            'previous_close' => round($price * fake()->randomFloat(2, 0.95, 1.05), 2),
        ];
    }
}