<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserTransactionController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $transactions = $user->transactions()
            ->with('giftCard')
            ->get(['id', 'type', 'points', 'gift_card_id', 'created_at']);

        return response()->json([
            'transactions' => $transactions,
        ]);
    }
}
