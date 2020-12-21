<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\UserResource;
use App\Models\GiftCard;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Bitcoin;
use App\Services\Robux;
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

        /** @var User $user */
        $user = auth()->user();
        $giftCard = null;
        $pointsValue = (int) Cache::get('points-value');

        abort_if(! $pointsValue, 422, 'Error during redeeming the reward!');
        abort_if(! $user->email_verified_at, 422, 'You need to verify your email in order to redeem.');

        if ($payload['provider'] === Transaction::TYPE_ROBUX) {
            $groupId = $this->storeRobuxTransaction($payload);

            if ($groupId !== 0) {
                return response()->json([
                    'group_id' => $groupId,
                ], 404);
            }
        } elseif ($payload['provider'] === Transaction::TYPE_BITCOIN) {
            $this->storeBitcoinTransaction($payload, $pointsValue);
        } else {
            $giftCard = $this->storeGiftCardTransaction($payload, $pointsValue);
        }

        return response()->json([
            'gift_card' => $giftCard,
            'user' => new UserResource($user->loadAvailablePoints()),
        ]);
    }

    private function storeGiftCardTransaction(array $payload, int $pointsValue): GiftCard
    {
        $giftCard = GiftCard::doesntHave('transaction')
            ->where('country', $payload['country'])
            ->where('value', $payload['value'])
            ->firstOrFail();

        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $giftCard->value * $pointsValue, 422, "You don't have enough points!");

        $user->transactions()->create([
            'type' => Transaction::TYPE_GIFT_CARD,
            'points' => $giftCard->value * $pointsValue,
            'gift_card_id' => $giftCard->id,
        ]);

        return $giftCard;
    }

    private function storeRobuxTransaction(array $payload): int
    {
        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $payload['value'], 422, "You don't have enough points!");

        $robuxAmount = Robux::getCurrency();

        abort_if(! $robuxAmount, 422, 'Robux is out of stock');
        abort_if($robuxAmount < $payload['value'], 422, "Only {$robuxAmount} robux is in stock");

        $group = Cache::get('robux');

        $payout = Robux::payout($group, $payload['destination'], $payload['value']);

        if (! $payout) {
            return $group['group_id'];
        }

        $user->transactions()->create([
            'type' => Transaction::TYPE_ROBUX,
            'points' => $payload['value'],
            'destination' => $payload['destination'],
            'value' => $payload['value'],
        ]);

        return 0;
    }

    private function storeBitcoinTransaction(array $payload, int $pointsValue): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $payload['value'] * $pointsValue, 422, "You don't have enough points!");

        $bitcoinAmount = (int) Bitcoin::getCurrency();
        $bitcoin = Cache::get('bitcoin');

        abort_if(! $bitcoin || $bitcoinAmount < $payload['value'] || $bitcoin['stock_amount'] < $payload['value'], 422, 'Bitcoin is out of stock');

        Bitcoin::payout($payload['destination'], $payload['value']);

        $user->transactions()->create([
            'type' => Transaction::TYPE_BITCOIN,
            'points' => $payload['value'] * $pointsValue,
            'destination' => $payload['destination'],
            'value' => $payload['value'],
        ]);
    }
}
