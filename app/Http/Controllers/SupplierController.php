<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = User::where('role', User::ROLE_SUPPLIER)
            ->with(['supplierGroup.supplierPayments', 'supplierGroup.transactions'])
            ->paginate(10);

        $pagination = $suppliers->toArray();
        $suppliersArr = $pagination['data'];
        unset($pagination['data']);

        return response()->json([
            'suppliers' => $suppliersArr,
            'pagination' => $pagination,
        ]);
    }
}
