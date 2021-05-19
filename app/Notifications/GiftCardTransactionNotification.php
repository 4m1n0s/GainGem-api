<?php

namespace App\Notifications;

use App\Models\GiftCard;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class GiftCardTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $transaction->load('giftCard');

        $this->transaction = $transaction;
    }

    public function via(User $user): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $user): MailMessage
    {
        /** @var GiftCard $giftCard */
        $giftCard = $this->transaction->giftCard;
        $currency = $giftCard->currency;
        $symbol = $currency ? $currency->symbol : '$';

        return (new MailMessage)
            ->subject("[GainGem] Successful Reward Claim #{$this->transaction->id}")
            ->greeting("Hello, {$user->username}!")
            ->line('Thank you for using GainGem!')
            ->line("You have successfully claimed a {$symbol}{$giftCard->value} {$this->transaction->formatted_provider} Gift Card for {$this->transaction->points} points. Please see your code below.")
            ->line(new HtmlString("<strong>{$giftCard->code}</strong>"))
            ->line('Share the news with your friends!');
    }

    public function toArray(User $user): array
    {
        return [
            'transaction_id' => $this->transaction->id,
        ];
    }
}
