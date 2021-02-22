<?php

namespace App\Http\Controllers;

use App\Models\RobuxAccount;
use Illuminate\Http\JsonResponse;

class RobuxAccountDisabilityController extends Controller
{
    public function store(RobuxAccount $robuxAccount): JsonResponse
    {
        abort_if((bool) $robuxAccount->disabled_at, 422, 'Account is already disabled');

        $robuxAccount->update([
            'disabled_at' => now(),
            'refresh_at' => null,
        ]);

        return response()->json($robuxAccount->append('formatted_disabled_at')->only('disabled_at', 'formatted_disabled_at'));
    }

    public function destroy(RobuxAccount $robuxAccount): JsonResponse
    {
        abort_if(! $robuxAccount->disabled_at, 422, 'Account is not disabled');

        $robuxAccount->update(['disabled_at' => null]);

        return response()->json($robuxAccount->only('disabled_at', 'formatted_disabled_at'));
    }
}
