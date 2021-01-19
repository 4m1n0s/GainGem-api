<?php

namespace App\Http\Controllers;

use App\Builders\RobuxGroupBuilder;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var Collection $suppliers */
        $suppliers = User::withTotalSupplierWithdrawals()
            ->where('role', User::ROLE_SUPPLIER)
            ->with(['robuxGroups' => static function (HasMany $query) {
                /** @var RobuxGroupBuilder $query */
                $query->select(['id', 'supplier_user_id'])->withTotalEarnings()->withTrashed();
            }])
            ->paginate(10);

        $suppliersArr = $suppliers->append(['formatted_robux_rate', 'total_supplier_withdrawals']);

        $suppliersArr->map(static function (User $supplier) {
            $supplier['formatted_groups_total_earnings'] = currency_format($supplier->robuxGroups->sum('total_earnings'));
            $supplier['formatted_total_supplier_withdrawals'] = currency_format($supplier->total_supplier_withdrawals);
            $supplier['formatted_groups_available_earnings'] = currency_format($supplier['formatted_groups_total_earnings'] - $supplier['formatted_total_supplier_withdrawals']);
        });

        $pagination = $suppliers->toArray();
        unset($pagination['data']);

        return response()->json([
            'suppliers' => $suppliersArr,
            'pagination' => $pagination,
        ]);
    }

    public function update(User $supplier, UpdateSupplierRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $supplier->update([
            'robux_rate' => $payload['robux_rate'] ? $payload['robux_rate'] / 1000 : null,
        ]);

        return response()->json($supplier->append('formatted_robux_rate'));
    }
}
