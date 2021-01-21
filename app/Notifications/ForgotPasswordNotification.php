<?php

namespace App\Notifications;

use App\Models\UrlToken;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotPasswordNotification extends Notification implements ShouldQueue
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
        $forgotPasswordUrl = $this->urlToken->getForgotPasswordUrl();

        return (new MailMessage)
            ->subject('Password reset request')
            ->greeting("Hello, {$user->username}!")
            ->line("You've requested to reset your password at ".config('app.name').'. Click on the link below to reset.')
            ->action('Reset Password', $forgotPasswordUrl)
            ->line('The link is valid for one day. You can request a new one if this one has expired.')
            ->line('If you did not submit this request, please ignore this email and delete it.');
    }

    public function toArray(User $user): array
    {
        return [
            'url_token_id' => $this->urlToken->id,
        ];
    }
}
