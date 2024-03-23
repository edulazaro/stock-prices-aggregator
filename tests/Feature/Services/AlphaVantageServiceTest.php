<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

use App\Services\AlphaVantageService;

class AlphaVantageServiceTest extends TestCase
{
    public function test_demo_key_error(): void
    {
        Http::fake([
            'www.alphavantage.co/query*' => Http::response([
                'Information' => 'The **demo** API key is for demo purposes only. Please claim your free API key at (https://www.alphavantage.co/support/#api-key) to explore our full API offerings. It takes fewer than 20 seconds.',
            ], 200),
        ]);

        $alphaVantageService = app()->make(AlphaVantageService::class);

        $response = $alphaVantageService->request([
            'function' => 'GLOBAL_QUOTE',
            'symbol' => 'IBM'
        ]);

        $this->assertArrayHasKey('error', $response);
    }

    public function test_rate_limit_error(): void
    {
        Http::fake([
            'www.alphavantage.co/query*' => Http::response([
                'Note' => 'Thank you for using Alpha Vantage! Our standard API call frequency is 5 calls per minute and 500 calls per day.',
            ], 200),
        ]);

        $alphaVantageService = app()->make(AlphaVantageService::class);

        $response = $alphaVantageService->request([
            'function' => 'GLOBAL_QUOTE',
            'symbol' => 'IBM'
        ]);

        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('rate limit', $response['error']);
    }

    public function test_standard_error(): void
    {
        Http::fake([
            'www.alphavantage.co/query*' => Http::response([
                'Error Message' => 'There was an error on the AlphaVantage API.',
            ], 200),
        ]);

        $alphaVantageService = app()->make(AlphaVantageService::class);

        $response = $alphaVantageService->request([
            'function' => 'GLOBAL_QUOTE',
            'symbol' => 'IBM'
        ]);

        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('There was an error on the', $response['error']);
    }

    public function test_server_error(): void
    {
        Http::fake([
            'www.alphavantage.co/query*' => Http::response(null, 500),
        ]);

        $alphaVantageService = app()->make(AlphaVantageService::class);

        $response = $alphaVantageService->request([
            'function' => 'GLOBAL_QUOTE',
            'symbol' => 'IBMX'
        ]);

        $this->assertArrayHasKey('error', $response);
        $this->assertStringContainsString('returned status code 500', $response['error']);
    }

    public function test_successful_request(): void
    {
        $responseArray = [
            'Global Quote' => [
                '01. symbol' => 'IBM',
                '02. open' => '192.8700',
                '03. high' => '193.9800',
                '04. low' => '191.3100',
                '05. price' => '193.9600',
                '06. volume' => '3238643',
                '07. latest trading day' => '2024-03-20',
                '08. previous close' => '193.3400',
                '09. change' => '0.6200',
                '10. change percent' => '0.3207%'
            ]
        ];

        Http::fake([
            'www.alphavantage.co/query*' => Http::response($responseArray, 200),
        ]);

        $alphaVantageService = app()->make(AlphaVantageService::class);

        $response = $alphaVantageService->request([
            'function' => 'GLOBAL_QUOTE',
            'symbol' => 'IBM'
        ]);

        $this->assertArrayHasKey('Global Quote', $response);
        $this->assertEquals($response, $responseArray);
    }
}
