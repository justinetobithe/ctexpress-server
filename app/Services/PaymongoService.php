<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymongoService
{
    private $http_client;
    public function __construct()
    {
        $this->http_client = Http::baseUrl(config('paymongo.api_url'))
            ->acceptJson()
            ->asJson()
            ->withBasicAuth(config('paymongo.secret_key'), '')
            ->throw();
    }

    /**
     * Create a Checkout Session.
     * 
     * @param string $payment_method
     * @param float $amount
     * @return array
     *  */
    public function createCheckoutSession($payment_method = '', $description = '', $amount = 0.00)
    {
        try {
            if (!$payment_method)
                throw new \Exception('INVALID PAYMENT METHOD');

            $api_response = $this->http_client->post('/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [
                            [
                                'amount' => (int) str_replace('.', '', (string) number_format((float) $amount, 2, '.', '')),
                                'name' => 'Vehicle Fare',
                                'quantity' => 1,
                                'currency' => 'PHP'
                            ]
                        ],
                        'payment_method_types' => [$payment_method],
                        'description' => $description
                    ]
                ]
            ])->object();

            return [
                'payment_intent_id' => $api_response->data->attributes->payment_intent->id ?? null,
                'url' => $api_response->data->attributes->checkout_url ?? ''
            ];
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}