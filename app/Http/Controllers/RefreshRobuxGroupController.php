<?php

namespace App\Http\Controllers;

use App\Models\RobuxGroup;
use App\Services\Robux;
use Illuminate\Http\JsonResponse;

class RefreshRobuxGroupController extends Controller
{
    public function store(RobuxGroup $robuxGroup): JsonResponse
    {
        $currency = Robux::getCurrency($robuxGroup);
        $robuxOwner = Robux::getUserByGroupId($robuxGroup->robux_group_id);

        $robuxGroup->update([
            'robux_amount' => $currency,
            'robux_owner_id' => $robuxOwner['userId'],
            'robux_owner_username' => $robuxOwner['displayName'],
            'disabled_at' => $currency >= RobuxGroup::MIN_ROBUX_AMOUNT ? null : now(),
        ]);

        return response()->json($robuxGroup->makeHidden('cookie'));
    }
}
