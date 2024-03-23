<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Service class responsible for fetching data from the Alpha Vantage API.
 * 
 * @author Edu Lazaro
 */
class AlphaVantageService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.alphavantage.api_key');
    }

    /**
     * Performs a request to the AlphaVantage API with given parameters.
     *
     * @param array $parameters Query parameters for the API request.
     * @return array The API response as an associative array.
     * @throws Exception If there's an error with the API request or response structure.
     */
    public function request(array $parameters = []): array
    {
        $parameters['apikey'] = $this->apiKey;

        try {
            $response = Http::timeout(3)->retry(3, function (int $attempt, Exception $exception) {

                Log::warning("Retry attempt #$attempt for AlphaVantage API due to {$exception->getMessage()}");
                return $attempt * 1000;
            })->get('https://www.alphavantage.co/query', $parameters);

            // Successful response
            if ($response->successful()) {

                $jsonResponse = $response->json();

                if (isset($jsonResponse['Information'])) {
                    throw new Exception("The demo API key was used.", $response->status());
                }

                if (isset($jsonResponse['Note'])) {
                    throw new Exception("There was a rate limit error on the AlphaVantage API.", $response->status());
                }

                if (isset($jsonResponse['Error Message'])) {
                    throw new Exception("There was an error on the AlphaVantage API.", $response->status());
                }

                return $jsonResponse;
            }

            throw new Exception("Server error {$response->status()} accessing the AlphaVantage API", $response->status());
            
        } catch (Exception $e) {

            $errorMessage = "Error: " . $e->getMessage() . ' | ' . $e->getCode();

            Log::error($errorMessage);
            return ['error' => $errorMessage];
        }
    }

    /**
     * Retrieves stock price data for a specific symbol.
     *
     * @param string $symbol The stock symbol to query.
     * @return array Detailed stock price information.
     * @throws Exception If the API response is invalid.
     */
    public function getStockPrice(string $symbol): array
    {
        $response = $this->request([
            'function' => 'GLOBAL_QUOTE',
            'symbol' => $symbol
        ]);

        if (!empty($response['error'])) return $response;

        if (!empty($response['Global Quote'])) {

            $stockData = $response['Global Quote'];

            return [
                'symbol' => $stockData['01. symbol'],
                'price' => $stockData['05. price'],
                'volume' => $stockData['06. volume'],
                'open' => $stockData['02. open'],
                'high' => $stockData['03. high'],
                'low' => $stockData['04. low'],
                'latest_trading_day' => $stockData['07. latest trading day'],
                'previous_close' => $stockData['08. previous close'],
                'change' => $stockData['09. change'],
                'change_percent' => $stockData['10. change percent'],
            ];
        }

        $message = 'The response structure of the AlphaVantage GLOBAL_QUOTE API endpoint was unexpected.';
        Log::error($message);
        return ['error' => $$message];
    }
}
