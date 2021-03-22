<?php

namespace App\Http\Controllers;

use App\Models\GiftCard;
use App\Models\RobuxAccount;
use App\Services\Bitcoin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class UserGiftCardController extends Controller
{
    public function index(): JsonResponse
    {
        $giftCards = GiftCard::doesntHave('transaction')
            ->whereHas('currency.currencyValue')
            ->groupBy('country', 'provider', 'value', 'currency_id')
            ->orderBy('country')
            ->get(['country', 'provider', 'value', 'currency_id'])
            ->groupBy('provider');

        $robuxAccount = RobuxAccount::whereNull('disabled_at')->exists();

        $stockAmount = (int) optional(Cache::get('bitcoin'))['stock_amount'];
        $bitcoinAmount = (int) Bitcoin::getCurrency();

        $bitcoinAmount = $bitcoinAmount > $stockAmount ? $stockAmount : $bitcoinAmount;

        return response()->json([
            'gift_cards' => $giftCards,
            'robux' => $robuxAccount,
            'bitcoin' => $bitcoinAmount,
        ]);
    }
}
