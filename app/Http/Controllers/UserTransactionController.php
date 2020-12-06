<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserTransactionController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $transactions = $user->transactions()
            ->with('giftCard')
            ->get(['id', 'type', 'points', 'gift_card_id', 'created_at'])
            ->each(static function (Transaction $transaction) {
                $transaction['formatted_created_at'] = $transaction->created_at ? $transaction->created_at->format('M d Y') : $transaction->created_at;
                $transaction['formatted_provider'] = $transaction->giftCard ? $transaction->giftCard->formatted_provider : ucfirst($transaction->type);
            });

        return response()->json([
            'transactions' => $transactions,
        ]);
    }
}
