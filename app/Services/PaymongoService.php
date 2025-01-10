<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
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
    public function createCheckoutSession($payment_method = '', $description = '', $amount = 0.00, $email = '', $name = '', $phone = ''): array
    {
        try {
            if (empty($payment_method)) {
                throw new \Exception('INVALID PAYMENT METHOD');
            }

            $billing = [
                "address" => [
                    "city" => null,
                    "country" => null,
                    "line1" => null,
                    "line2" => null,
                    "postal_code" => null,
                    "state" => null
                ],
                "email" => $name ?? null,
                "phone" => $phone ?? null,
                "name" => $email ?? null,
            ];

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
                        'description' => $description,
                        'billing' => $billing,
                    ]
                ]
            ])->object();

            return [
                // 'data' => $api_response->data,
                'payment_intent_id' => $api_response->data->attributes->payment_intent->id ?? null,
                'url' => $api_response->data->attributes->checkout_url ?? '',

            ];
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}
