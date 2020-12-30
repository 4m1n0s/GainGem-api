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
            ->select(['id', 'username'])
            ->with(['robuxGroups' => static function (HasMany $query) {
                /** @var RobuxGroupBuilder $query */
                $query->select(['id', 'supplier_user_id'])->withAvailableEarnings();
            }])
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
