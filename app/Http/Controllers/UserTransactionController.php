<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\UserResource;
use App\Models\GiftCard;
use App\Models\RobuxAccount;
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
            ->get(['id', 'type', 'points', 'gift_card_id', 'destination', 'robux_amount', 'bitcoin_amount', 'created_at', 'emailed_at'])
            ->append('has_emailed_in_the_last_hour');

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
        $pointsValue = (int) ($payload['provider'] === Transaction::TYPE_BITCOIN ? Cache::get('bitcoin-value') : Cache::get('points-value'));

        abort_if(! $pointsValue, 422, 'Error during redeeming the reward!');
//        abort_if(! $user->email_verified_at, 422, 'You need to verify your email in order to redeem.');

        if ($payload['provider'] === Transaction::TYPE_ROBUX) {
            abort_if($payload['value'] < 7, 422, 'The amount has to be greater than 6.');

            $this->storeRobuxTransaction($payload);
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
            ->where('provider', $payload['provider'])
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

        if ($user->email_verified_at) {
            $user->notify(new GiftCardTransactionNotification($transaction));
        }

        return $giftCard;
    }

    private function storeRobuxTransaction(array $payload): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        abort_if($user->available_points < $payload['value'], 422, "You don't have enough points!");

        $robuxAccountsExist = RobuxAccount::whereNull('disabled_at')->exists();

        abort_if(! $robuxAccountsExist, 422, 'Robux is out of stock.');

        $value = (int) ceil($payload['value'] / 0.7);

        /** @var Collection $robuxAccounts */
        $robuxAccounts = RobuxAccount::bestMatch()->where('robux_amount', '>', $value)->get();
        $i = 0;

        $chosenAccount = null;

        while (! $chosenAccount && $i < count($robuxAccounts)) {
            $robuxAmount = Robux::getCurrency($robuxAccounts[$i]);

            $robuxAccounts[$i]->update([
                'robux_amount' => $robuxAmount,
                'disabled_at' => $robuxAmount < RobuxAccount::MIN_ROBUX_AMOUNT ? now() : null,
            ]);

            if ($robuxAmount >= $payload['value']) {
                $chosenAccount = $robuxAccounts[$i];
            }

            $i++;
        }

        abort_if(! $chosenAccount, 422, "Couldn't provide enough robux for your requested amount.");

        /** @var RobuxAccount $chosenAccount */
        Robux::payout($chosenAccount, $payload['game_id'], $value);

        $rate = $chosenAccount->supplierUser->robux_rate ?? Cache::get('robux-supplier-rate');

        $user->transactions()->create([
            'type' => Transaction::TYPE_ROBUX,
            'points' => $payload['value'],
            'destination' => $payload['destination'],
            'value' => $value * $rate,
            'robux_account_id' => $chosenAccount->id,
            'robux_amount' => $value,
        ]);
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

        if ($user->email_verified_at) {
            $user->notify(new BitcoinTransactionNotification($transaction, $bitcoinPayoutResponse['tx_hash']));
        }
    }
}
