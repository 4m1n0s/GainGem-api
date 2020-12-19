<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\UserResource;
use App\Models\GiftCard;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class UserTransactionController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $transactions = $user->transactions()
            ->with('giftCard:id,provider')
            ->orderByDesc('id')
            ->get(['id', 'type', 'points', 'gift_card_id', 'created_at']);

        return response()->json([
            'transactions' => $transactions,
        ]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $user = auth()->user();
        $giftCard = null;
        $pointsValue = (int) Cache::get('points-value');

        abort_if(! $pointsValue, 422, 'Error during redeeming the reward!');
        abort_if(! $user->email_verified_at, 422, 'You need to verify your email in order to redeem.');

        if ($payload['provider'] === Transaction::TYPE_ROBUX) {
            $this->storeRobuxTransaction($payload, $pointsValue);
        } elseif ($payload['provider'] === Transaction::TYPE_BITCOIN) {
            $this->storeBitcoinTransaction($payload, $pointsValue);
        } else {
            $giftCard = $this->storeGiftCardTransaction($payload, $pointsValue);
        }

        return response()->json([
            'gift_card' => $giftCard,
            'user' => new UserResource(auth()->user()->loadAvailablePoints()),
        ]);
    }

    private function storeGiftCardTransaction(array $payload, int $pointsValue): GiftCard
    {
        $giftCard = GiftCard::doesntHave('transaction')
            ->where('country', $payload['country'])
            ->where('value', $payload['value'])
            ->firstOrFail();

        $user = auth()->user()->loadAvailablePoints();

        abort_if($user->available_points < $giftCard->value * $pointsValue, 422, "You don't have enough points!");

        $user->transactions()->create([
            'type' => Transaction::TYPE_GIFT_CARD,
            'points' => $giftCard->value * $pointsValue,
            'gift_card_id' => $giftCard->id,
        ]);

        return $giftCard;
    }

    private function storeRobuxTransaction(array $payload, int $pointsValue): void
    {
    }

    private function storeBitcoinTransaction(array $payload, int $pointsValue): void
    {
    }
}
