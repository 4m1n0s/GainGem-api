<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BitcoinTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Transaction $transaction;
    public string $hash;

    public function __construct(Transaction $transaction, string $hash)
    {
        $this->transaction = $transaction;
        $this->hash = $hash;
    }

    public function via(User $user): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $user): MailMessage
    {
        return (new MailMessage)
            ->subject("[EzRewards] Successful Reward Claim #{$this->transaction->id}")
            ->greeting("Hello, {$user->username}!")
            ->line('Thank you for using EzRewards!')
            ->line("You have successfully claimed \${$this->transaction->value} Bitcoin for {$this->transaction->points} points. Please allow some time for the transaction to confirm.")
            ->action('Transaction Status', "https://www.blockchain.com/btc/tx/{$this->hash}")
            ->line('Share the news with your friends!');
    }

    public function toArray(User $user): array
    {
        return [
            'transaction_id' => $this->transaction->id,
        ];
    }
}
