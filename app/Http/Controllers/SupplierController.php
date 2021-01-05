<?php

namespace App\Http\Controllers;

use App\Builders\RobuxGroupBuilder;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = User::where('role', User::ROLE_SUPPLIER)
            ->withTotalSupplierWithdrawals()
            ->with(['robuxGroups' => static function (HasMany $query) {
                /** @var RobuxGroupBuilder $query */
                $query->select(['id', 'supplier_user_id'])->withTotalEarnings();
            }])
            ->paginate(10);

        $suppliersArr = $suppliers->append(['formatted_robux_rate', 'total_supplier_withdrawals']);

        $suppliersArr->map(static function (User $supplier) {
            $supplier->formatted_groups_total_earnings = currency_format($supplier->robuxGroups->sum('total_earnings'));
            $supplier->formatted_total_supplier_withdrawals = currency_format($supplier->total_supplier_withdrawals);
            $supplier->formatted_groups_available_earnings = currency_format($supplier->formatted_groups_total_earnings - $supplier->formatted_total_supplier_withdrawals);
        });

        $pagination = $suppliers->toArray();
        unset($pagination['data']);

        return response()->json([
            'suppliers' => $suppliersArr,
            'pagination' => $pagination,
        ]);
    }
}
