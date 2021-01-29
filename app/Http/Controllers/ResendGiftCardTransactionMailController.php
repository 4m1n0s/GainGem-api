<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Notifications\GiftCardTransactionNotification;
use Illuminate\Support\Facades\Gate;

class ResendGiftCardTransactionMailController extends Controller
{
    public function store(Transaction $transaction): void
    {
        Gate::authorize('mail', $transaction);

        $user = $transaction->user;

        $isNotificationExist = optional($transaction->emailed_at)->between(now()->startOfHour(), now()->endOfHour());

        $maxFiveNotifications = $user->transactions()
            ->whereBetween('emailed_at', [now()->startOfHour(), now()->endOfHour()])
            ->limit(5)
            ->get(['user_id', 'emailed_at']);

        abort_if($isNotificationExist, 422, 'You can request a mail once an hour.');
        abort_if(count($maxFiveNotifications) >= 5, 422, 'You can request only 5 mails in an hour.');

        $user->notify(new GiftCardTransactionNotification($transaction));

        $transaction->update([
            'emailed_at' => now(),
        ]);
    }
}
