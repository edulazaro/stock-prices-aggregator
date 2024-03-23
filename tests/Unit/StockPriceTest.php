<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Models\StockPrice;

class StockPriceTest extends TestCase
{
    public function test_percentage_change_attribute()
    {
        $stockPrice = new StockPrice();
        $stockPrice->price = 100.0;
        $stockPrice->previous_close = 90.0;

        $expectedPercentageChange = round((100.0 - 90.0) / 90.0 * 100, 4);

        $this->assertEquals($expectedPercentageChange, $stockPrice->percentageChange);
    }
}
