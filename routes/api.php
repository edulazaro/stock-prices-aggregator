<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\StockPriceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/stock-prices', [StockPriceController::class, 'index']);
Route::get('/stock-prices/{symbol}', [StockPriceController::class, 'show']);
