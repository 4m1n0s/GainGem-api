<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\UserResource;
use App\Models\GiftCard;
use App\Models\RobuxGroup;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BitcoinTransactionNotification;
use App\Notifications\GiftCardTransactionNotification;
use App\Services\Bitcoin;
use App\Services\Robux;
use Illuminate\Database\Eloquent\Collection;
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
            $chosenGroupId = $this->storeRobuxTransaction($payload);

            if ($chosenGroupId !== 0) {
                return response()->json([
                    'group_id' => $chosenGroupId,
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

        /** @var Transaction $transaction */
        $transaction = $user->transactions()->create([
            'type' => Transaction::TYPE_GIFT_CARD,
            'points' => $giftCard->value * $pointsValue,
            'gift_card_id' => $giftCard->id,
        ]);

        $user->notify(new GiftCardTransactionNotification($transaction));

        return $giftCard;
    }

    private function storeRobuxTransaction(array $payload): int
    {
        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $payload['value'], 422, "You don't have enough points!");

        $robuxGroupsExist = RobuxGroup::whereNull('disabled_at')->exists();

        abort_if(! $robuxGroupsExist, 422, 'Robux is out of stock.');

        $chosenGroup = null;

        if (isset($payload['group_id'])) {
            $chosenGroup = RobuxGroup::whereNull('disabled_at')->where('robux_group_id', $payload['group_id'])->first();

            if ($chosenGroup) {
                /** @var RobuxGroup $chosenGroup */
                $robuxAmount = Robux::getCurrency($chosenGroup);

                $chosenGroup->update([
                    'robux_amount' => $robuxAmount,
                    'disabled_at' => $robuxAmount < RobuxGroup::MIN_ROBUX_AMOUNT ? now() : null,
                ]);

                if ($robuxAmount < $payload['value']) {
                    $chosenGroup = null;
                }
            }
        }

        if (! $chosenGroup) {
            /** @var Collection $robuxGroups */
            $robuxGroups = RobuxGroup::bestMatch()->where('robux_amount', '>', $payload['value'])->get();
            $i = 0;

            while (! $chosenGroup && $i < count($robuxGroups)) {
                $robuxAmount = Robux::getCurrency($robuxGroups[$i]);

                $robuxGroups[$i]->update([
                    'robux_amount' => $robuxAmount,
                    'disabled_at' => $robuxAmount < RobuxGroup::MIN_ROBUX_AMOUNT ? now() : null,
                ]);

                if ($robuxAmount >= $payload['value']) {
                    $chosenGroup = $robuxGroups[$i];
                }

                $i++;
            }
        }

        abort_if(! $chosenGroup, 422, "Couldn't find a group with the amount you've asked.");

        $robuxPayout = Robux::payout($chosenGroup, $payload['destination'], $payload['value']);

        if (! $robuxPayout) {
            return $chosenGroup->robux_group_id;
        }

        $rate = $chosenGroup->supplierUser->robux_rate ?? Cache::get('robux-supplier-rate');

        $user->transactions()->create([
            'type' => Transaction::TYPE_ROBUX,
            'points' => $payload['value'],
            'destination' => $payload['destination'],
            'value' => $payload['value'] * $rate,
            'robux_group_id' => $chosenGroup->id,
            'robux_amount' => $payload['value'],
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

        abort_if(! $bitcoin, 422, 'Bitcoin is out of stock');
        abort_if($bitcoinAmount < $payload['value'], 422, 'Bitcoin is out of stock');
        abort_if($bitcoin['stock_amount'] < $payload['value'], 422, 'Bitcoin is out of stock');

        $bitcoin = get_bitcoin_value() * $payload['value'];
        $satoshi = $bitcoin * pow(10, 8);
        $bitcoinPayoutResponse = Bitcoin::payout($payload['destination'], (int) $satoshi);

        /** @var Transaction $transaction */
        $transaction = $user->transactions()->create([
            'type' => Transaction::TYPE_BITCOIN,
            'points' => $payload['value'] * $pointsValue,
            'destination' => $payload['destination'],
            'value' => $payload['value'],
            'bitcoin_amount' => $bitcoin,
        ]);

        $user->notify(new BitcoinTransactionNotification($transaction, $bitcoinPayoutResponse['tx_hash']));
    }
}
