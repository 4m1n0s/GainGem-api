<?php

namespace App\Notifications;

use App\Models\UrlToken;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public UrlToken $urlToken;

    public function __construct(UrlToken $urlToken)
    {
        $this->urlToken = $urlToken;
    }

    public function via(User $user): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $user): MailMessage
    {
        $verifyUrl = $this->urlToken->getVerificationUrl();

        return (new MailMessage)
            ->subject('Email verification')
            ->greeting("Hello, {$user->username}!")
            ->line('Thanks for registering to '.config('app.name'))
            ->line("Click on the link down below to verify your account. We just need to verify that you're a human.")
            ->action("I'm a human", $verifyUrl);
    }

    public function toDatabase(User $user): array
    {
        return [
            'url_token_id' => $this->urlToken->id,
        ];
    }
}
