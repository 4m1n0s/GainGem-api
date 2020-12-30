<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = User::where('role', User::ROLE_SUPPLIER)
            ->select(['id', 'username'])
            ->with([
                'supplierGroup:id,user_id',
                'supplierGroup.supplierPayments:supplier_group_id,value,status',
                'supplierGroup.transactions:supplier_group_id,value',
            ])
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
