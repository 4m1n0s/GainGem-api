<?php

namespace App\Http\Controllers;

use App\Models\RobuxAccount;
use Illuminate\Http\JsonResponse;

class RefreshRobuxAccountController extends Controller
{
    public function store(RobuxAccount $robuxAccount): JsonResponse
    {
        $robuxAccount->refreshData();

        return response()->json($robuxAccount->makeHidden('cookie'));
    }
}
