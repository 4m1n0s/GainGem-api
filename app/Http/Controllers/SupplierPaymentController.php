<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSupplierPaymentRequest;
use App\Models\SupplierPayment;
use Illuminate\Http\JsonResponse;

class SupplierPaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $supplierPayments = SupplierPayment::with('supplierUser:id,username')->orderByDesc('id')->paginate(10);

        $pagination = $supplierPayments->toArray();
        $supplierPaymentsArr = $pagination['data'];
        unset($pagination['data']);

        return response()->json([
            'payments' => $supplierPaymentsArr,
            'pagination' => $pagination,
        ]);
    }

    public function update(SupplierPayment $supplierPayment, UpdateSupplierPaymentRequest $request): JsonResponse
    {
        $payload = $request->validated();

        if ($payload['status'] !== SupplierPayment::STATUS_DENIED) {
            $payload['denial_reason'] = null;
        }

        $supplierPayment->update($payload);

        return response()->json($supplierPayment);
    }
}
