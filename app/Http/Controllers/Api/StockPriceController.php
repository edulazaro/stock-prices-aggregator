<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

use App\Repositories\StockPriceRepository;

class StockPriceController extends Controller
{
    protected $stockPriceRepository;

    public function __construct(StockPriceRepository $stockPriceRepository)
    {
        $this->stockPriceRepository = $stockPriceRepository;
    }

    /**
     * Display a listing of the stock prices.
     */
    public function index(): JsonResponse
    {
        try {
            $stockPrices = $this->stockPriceRepository->all();
    
            return response()->json([
                'success' => true,
                'data' => $stockPrices
            ], 200);

        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage()
            ], 200);
        }
    }
    /**
     * Return the specified stock price.
     */
    public function show(string $symbol): JsonResponse
    {
        $stockPrice =  $this->stockPriceRepository->getBySymbol($symbol);

        if (!$stockPrice) {
            return response()->json([
                'success' => false,
                'error' => 'Stock price not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stockPrice
        ], 200);
    }
}
