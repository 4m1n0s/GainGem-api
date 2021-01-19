<?php

namespace App\Http\Controllers;

use App\Models\RobuxGroup;
use Illuminate\Http\JsonResponse;

class RobuxGroupDisabilityController extends Controller
{
    public function store(RobuxGroup $robuxGroup): JsonResponse
    {
        abort_if((bool) $robuxGroup->disabled_at, 422, 'Group is already disabled');

        $robuxGroup->update(['disabled_at' => now()]);

        return response()->json($robuxGroup->append('formatted_disabled_at')->only('disabled_at', 'formatted_disabled_at'));
    }

    public function destroy(RobuxGroup $robuxGroup): JsonResponse
    {
        abort_if(! $robuxGroup->disabled_at, 422, 'Group is not disabled');

        $robuxGroup->update(['disabled_at' => null]);

        return response()->json($robuxGroup->only('disabled_at', 'formatted_disabled_at'));
    }
}
