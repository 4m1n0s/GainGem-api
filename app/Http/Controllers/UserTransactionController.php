<?php

namespace App\Http\Controllers;

use App\Domains\Robux\Actions\GetRelevantAccountAction;
use App\Domains\Robux\Actions\PayoutAction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\UserResource;
use App\Models\Currency;
use App\Models\CurrencyValue;
use App\Models\GiftCard;
use App\Models\RobuxAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BitcoinTransactionNotification;
use App\Notifications\GiftCardTransactionNotification;
use App\Services\Bitcoin;
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
        $lock = Cache::lock("transaction.{$user->id}", 10);

        abort_if((bool) $user->froze_at, 422, 'Your account is currently frozen. Please contact support in order to redeem rewards.');
        abort_if(! $lock->get(), 422, "You're already in the process of redeeming!");

        $giftCard = null;

        if ($payload['provider'] === Transaction::TYPE_ROBUX) {
            abort_if($payload['value'] < 7, 422, 'The amount has to be greater than 6.');

            $this->storeRobuxTransaction($payload);
        } elseif ($payload['provider'] === Transaction::TYPE_BITCOIN) {
            $this->storeBitcoinTransaction($payload);
        } else {
            $giftCard = $this->storeGiftCardTransaction($payload);
        }

        return response()->json([
            'gift_card' => $giftCard,
            'user' => new UserResource($user->loadAvailablePoints()),
        ]);
    }

    private function storeGiftCardTransaction(array $payload): GiftCard
    {
        $giftCard = GiftCard::doesntHave('transaction')
            ->whereHas('currency.currencyValue')
            ->where('provider', $payload['provider'])
            ->where('country', $payload['country'])
            ->where('currency_id', $payload['currency_id'])
            ->where('value', $payload['value'])
            ->with('currency.currencyValue')
            ->firstOrFail();

        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        /** @var Currency $currency */
        $currency = $giftCard->currency;
        /** @var CurrencyValue $currencyValue */
        $currencyValue = $currency->currencyValue;
        $giftCardValue = $currencyValue[$payload['provider']];

        abort_if(bccomp((string) $user->available_points, (string) ($giftCard->value * $giftCardValue)) === -1, 422, "You don't have enough points!");

        /** @var Transaction $transaction */
        $transaction = $user->transactions()->create([
            'type' => Transaction::TYPE_GIFT_CARD,
            'points' => $giftCard->value * $giftCardValue,
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

        abort_if(bccomp((string) $user->available_points, (string) $payload['value']) === -1, 422, "You don't have enough points!");

        $robuxAccountsExist = RobuxAccount::whereNull('disabled_at')->exists();

        abort_if(! $robuxAccountsExist, 422, 'Robux is out of stock.');

        $value = (int) ceil($payload['value'] / 0.7);

        $robuxAccount = (new GetRelevantAccountAction)->execute($value);

        abort_if(! $robuxAccount, 422, "Couldn't provide enough robux for your requested amount.");

        (new PayoutAction)->execute($robuxAccount, $payload['game_id'], $value);

        $rate = $robuxAccount->supplierUser->robux_rate ?? Cache::get('robux-supplier-rate');

        $user->transactions()->create([
            'type' => Transaction::TYPE_ROBUX,
            'points' => $payload['value'],
            'destination' => $payload['destination'],
            'value' => $value * $rate,
            'robux_account_id' => $robuxAccount->id,
            'robux_amount' => $value,
        ]);
    }

    private function storeBitcoinTransaction(array $payload): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->loadAvailablePoints();

        $bitcoinValue = Cache::get('bitcoin-value');

        abort_if(bccomp((string) $user->available_points, (string) ($payload['value'] * $bitcoinValue)) === -1, 422, "You don't have enough points!");

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
            'points' => $payload['value'] * $bitcoinValue,
            'destination' => $payload['destination'],
            'value' => $payload['value'],
            'bitcoin_amount' => $bitcoin,
        ]);

        if ($user->email_verified_at) {
            $user->notify(new BitcoinTransactionNotification($transaction, $bitcoinPayoutResponse['tx_hash']));
        }
    }
}
