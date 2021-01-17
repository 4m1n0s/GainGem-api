<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRobuxGroupRequest;
use App\Http\Requests\StoreRobuxGroupRequest;
use App\Models\RobuxGroup;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Robux;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RobuxGroupController extends Controller
{
    public function index(IndexRobuxGroupRequest $request): JsonResponse
    {
        $payload = $request->validated();

        if (! isset($payload['user_id'])) {
            $robuxGroups = RobuxGroup::withTotalWithdrawn()->orderByDesc('id')->paginate(10);

            $robuxGroupsArr = $robuxGroups->append('formatted_total_withdrawn', 'formatted_disabled_at');

            $robuxGroupsArr->map(static function (RobuxGroup $robuxGroup) {
                $robuxGroup->cookie = Str::limit($robuxGroup->cookie, 200);
            });

            $pagination = $robuxGroups->toArray();
            unset($pagination['data']);

            return response()->json([
                'groups' => $robuxGroupsArr,
                'pagination' => $pagination,
            ]);
        }

        $supplier = User::find($payload['user_id']);
        $this->authorize('update', $supplier);

        $robuxGroups = $supplier->robuxGroups()->select(['id', 'supplier_user_id', 'robux_group_id', 'robux_owner_username', 'robux_amount', 'disabled_at'])
            ->orderByDesc('id')
            ->paginate(10);

        $monthlyRobuxEarnings = Transaction::whereSupplierWithTrashed($supplier)
            ->selectRaw('DATE(`created_at`) as date, SUM(`robux_amount`) as total_robux_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->whereBetween('created_at', [now()->startOfMonth()->startOfDay(), now()->endOfMonth()->endOfDay()])
            ->get()
            ->each(static function (Transaction $transaction) {
                $date = Carbon::parse($transaction->date);
                $transaction->formatted_date = $date->copy()->format('M d');
                $transaction->date = $date->day;
            });

        $totalRobuxEarnings = Transaction::whereSupplierWithTrashed($supplier)->sum('robux_amount');

        $robuxGroupsArr = $robuxGroups->append('formatted_disabled_at');

        $pagination = $robuxGroups->toArray();
        unset($pagination['data']);

        return response()->json([
            'groups' => $robuxGroupsArr,
            'pagination' => $pagination,
            'monthly_robux_earnings' => $monthlyRobuxEarnings,
            'total_robux_earnings' => currency_format((float) $totalRobuxEarnings),
        ]);
    }

    public function store(StoreRobuxGroupRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $responseGroupSettings = Robux::getGroupSettingsResponse($payload['cookie'], $payload['robux_group_id']);

        abort_if($responseGroupSettings->status() === 401, 422, 'Invalid cookie!');
        abort_if($responseGroupSettings->failed(), 422, 'Group not found!');

        $robuxGroupOwner = Robux::getUserByGroupId($payload['robux_group_id']);
        $robuxGroupCurrency = Robux::getCurrency([
            'robux_group_id' => $payload['robux_group_id'],
            'cookie' => $payload['cookie'],
        ]);

        abort_if($robuxGroupCurrency < RobuxGroup::MIN_ROBUX_AMOUNT, 422, 'Group must have at least '.RobuxGroup::MIN_ROBUX_AMOUNT.' robux.');

        $robuxGroup = auth()->user()->robuxGroups()->create([
            'cookie' => $payload['cookie'],
            'robux_group_id' => $payload['robux_group_id'],
            'robux_owner_id' => $robuxGroupOwner['userId'],
            'robux_owner_username' => $robuxGroupOwner['displayName'],
            'robux_amount' => $robuxGroupCurrency,
        ]);

        return response()->json($robuxGroup->makeHidden('cookie'));
    }

    public function destroy(RobuxGroup $robuxGroup): void
    {
        $this->authorize('update', auth()->user());

        $robuxGroup->delete();
    }
}
