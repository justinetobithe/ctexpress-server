<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Services\GCashService;
use App\Services\PayMayaService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $gcashService;
    protected $payMayaService;

    public function __construct(GCashService $gcashService, PayMayaService $payMayaService)
    {
        $this->gcashService = $gcashService;
        $this->payMayaService = $payMayaService;
    }

    public function store(PaymentRequest $request)
    {
        $amount = $request->input('amount');
        $paymentMethod = $request->input('payment_method');
        $bookingId = $request->input('booking_id');

        $response = null;
        $transactionId = null;

        switch ($paymentMethod) {
            case 'gcash':
                $response = $this->gcashService->createPayment($amount);
                $transactionId = $response['transaction_id'] ?? null;
                break;

            case 'paymaya':
                $response = $this->payMayaService->createPayment($amount);
                $transactionId = $response['transaction_id'] ?? null;
                break;

            case 'cash':
                $response = [
                    'status' => true,
                    'message' => 'Cash payment selected. Please proceed with cash payment.',
                ];
                break;

            default:
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid payment method.',
                ], 400);
        }

        Payment::create([
            'booking_id' => $bookingId,
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'transaction_id' => $transactionId,
        ]);

        return response()->json($response);
    }
}
