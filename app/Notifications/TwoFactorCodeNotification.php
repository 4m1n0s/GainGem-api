<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TwoFactorCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(User $user): array
    {
        return ['mail'];
    }

    public function toMail(User $user): MailMessage
    {
        return (new MailMessage)
            ->subject('Two Factor Code')
            ->greeting("Hello, {$user->username}!")
            ->line(new HtmlString("Your two factor code is <strong>{$user->two_factor_code}</strong>."))
            ->line('The code will expire in 10 minutes. If you have not tried to login, ignore this message.');
    }
}
