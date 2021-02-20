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
            ->groupBy('country', 'provider', 'value')
            ->orderBy('country')
            ->get(['country', 'provider', 'value'])
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
