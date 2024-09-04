<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayMayaService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.paymaya.base_url');
        $this->apiKey = config('services.paymaya.api_key');
    }

    public function createPayment($amount, $currency = 'PHP')
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/v1/charges", [
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return $response->json();
    }
}
