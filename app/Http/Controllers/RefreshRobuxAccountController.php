<?php

namespace App\Http\Controllers;

use App\Models\RobuxAccount;
use App\Services\Robux;
use Illuminate\Http\JsonResponse;

class RefreshRobuxAccountController extends Controller
{
    public function store(RobuxAccount $robuxAccount): JsonResponse
    {
        $currency = Robux::getCurrency($robuxAccount);
        $robuxUser = Robux::getUserById($robuxAccount->robux_account_id);

        $robuxAccount->update([
            'robux_amount' => $currency,
            'robux_account_username' => $robuxUser['Username'],
            'disabled_at' => $currency >= RobuxAccount::MIN_ROBUX_AMOUNT ? null : now(),
        ]);

        return response()->json($robuxAccount->makeHidden('cookie'));
    }
}
