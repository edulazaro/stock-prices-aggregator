<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

use App\Services\AlphaVantageService;
use App\Repositories\StockPriceRepository;

/**
 * Console command for updating stock market prices.
 *
 * This command is responsible for fetching the latest stock market prices for a predefined list of stock symbols
 * The command runs every minute.
 * 
 * Usage: php artisan stocks:update
 *
 * @author Edu Lazaro
 */
class UpdateStocksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the updated stock market prices';

    /**
     * Fetch the stock prices using the AlphaVantageService and update the database and the cache accordingly,
     *
     * This method retrieves the fixed list of stock symbols, and then uses the AlphaVantageService API service
     * the get the updated prices. If a symbol does not exist, then it will be created. The collection of
     * symbols is finally cached for 60 seconds. It could be cashed forever, till there is another update,
     * however a time restriction was specified as a requirement.
     *
     * @param AlphaVantageService $alphaVantageService The Alpha Vantage API service.
     * @param StockPriceRepository $stockPriceRepository The repository managing StockPrice database records.
     * @return void
     */
    public function handle(AlphaVantageService $alphaVantageService, StockPriceRepository $stockPriceRepository): void
    {
        $stockSymbols = config('services.alphavantage.stock_symbols');

        foreach ($stockSymbols as $stockSymbol) {
            $stockData = $alphaVantageService->getStockPrice($stockSymbol);

            if (!empty($stockData['error'])) {
                $this->error("An error happened when getting the price for  $stockSymbol:" . $stockData['error']);
                continue;
            }
            
            $this->info("The price $stockSymbol was correctly retrieved.");

            $stockPriceRepository->updateOrCreate(
                [
                    'symbol' => $stockData['symbol']
                ],
                [
                    'price' => $stockData['price'],
                    'volume' => $stockData['volume'],
                    'open' => $stockData['open'],
                    'high' => $stockData['high'],
                    'low' => $stockData['low'],
                    'previous_close' => $stockData['previous_close'],
                ]
            );
        }

        // Cache the last price for each stock
        $stockPriceRepository->all();
    }
}
