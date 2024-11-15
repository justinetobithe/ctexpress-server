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

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'payment_method');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Payment::with(['user', 'booking']);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('payment_method', 'like', "%{$filter}%")
                    ->orWhere('amount', 'like', "%{$filter}%")
                    ->orWhere('reference_no', 'like', "%{$filter}%");
            });
        }

        $query->orderBy('id', 'desc');

        if (in_array($sortColumn, ['name', 'payment_method', 'amount', 'reference_no'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }

        if ($pageSize) {
            $payments = $query->paginate($pageSize);
        } else {
            $payments = $query->get();
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.fetched'),
            'data' => $payments,
        ]);
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
