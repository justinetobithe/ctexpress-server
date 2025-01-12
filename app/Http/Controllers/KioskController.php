<?php

namespace App\Http\Controllers;

use App\Http\Requests\KioskRequest;
use App\Models\kiosk;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KioskController extends Controller
{

    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size');
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'date');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Kiosk::with(['trip.terminalFrom', 'trip.terminalTo']);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', "%{$filter}%")
                    ->orWhere('email', 'like', "%{$filter}%")
                    ->orWhere('phone', 'like', "%{$filter}%")
                    ->orWhere('date', 'like', "%{$filter}%")
                    ->orWhere('amount_to_pay', 'like', "%{$filter}%");
            });
        }

        $query->orderBy('id', 'desc');

        if (in_array($sortColumn, ['name', 'email', 'phone', 'date', 'amount_to_pay'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }

        if ($pageSize) {
            $trips = $query->paginate($pageSize);
        } else {
            $trips = $query->get();
        }

        return $this->success($trips);
    }

    public function store(KioskRequest $request)
    {
        $validated = $request->validated();

        $validated['uuid'] = mt_rand(10000, 99999);
        $validated['paid'] = $request->payment_method === 'Cash' ? 0 : 1;
        $validated['date'] = now()->toDateString();

        $kiosk = Kiosk::create($validated);

        $referenceNo = 'REF' . strtoupper(Str::random(5)) . $kiosk->id;

        if ($request->payment !== "Cash") {
            Payment::create([
                'kiosk_id' => $kiosk->id,
                'payment_method' => $kiosk->payment_method,
                'amount' => $kiosk->amount_to_pay,
                'reference_no' => $referenceNo,
            ]);
        }

        $kiosk->load(['trip.terminalFrom', 'trip.terminalTo']);

        return response()->json([
            'status' => 'success',
            'message' => __('Kiosk created successfully with ID: ') . $kiosk->uuid,
            'data' => $kiosk,
        ]);
    }

    public function markAsPaid(Request $request, string $id)
    {
        $kiosk = Kiosk::findOrFail($id);

        $kiosk->paid = 1;
        $kiosk->save();

        $referenceNo = 'REF' . strtoupper(Str::random(5)) . $kiosk->id;

        Payment::create([
            'kiosk_id' => $kiosk->id,
            'payment_method' => $kiosk->payment_method,
            'amount' => $kiosk->amount_to_pay,
            'reference_no' => $referenceNo,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('Payment successfully recorded and kiosk updated to paid'),
            'data' => $kiosk,
        ]);
    }
}
