<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GCashService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.gcash.base_url');
        $this->apiKey = config('services.gcash.api_key');
    }

    public function createPayment($amount, $currency = 'PHP')
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/v1/transactions", [
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return $response->json();
    }
}
