<?php

namespace App\Notifications;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class LoginLogChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public LoginLog $loginLog;

    public function __construct(LoginLog $loginLog)
    {
        $this->loginLog = $loginLog;
    }

    public function via(User $user): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $user): MailMessage
    {
        $changePasswordUrl = config('app.user_app_url').'/forgot-password';
        $timezone = config('app.timezone');

        return (new MailMessage)
            ->subject('[GainGem] Successful log-in')
            ->greeting("Hello, {$user->username}!")
            ->line("This email was generated because a new log-in has occurred on {$this->loginLog->formatted_created_at} {$timezone} originating from:")
            ->line(new HtmlString($this->formattedOrigins()))
            ->line('If you initiated this log-in, awesome! We just wanted to make sure it’s you.')
            ->line(new HtmlString("If you did NOT initiate this log-in, you should immediately <a href='{$changePasswordUrl}' target='_blank'>change your GainGem password</a> to ensure account security."))
            ->line(new HtmlString('We <strong>strongly recommend</strong> that you enable two-factor authentication if you haven’t already.'));
    }

    public function toArray(User $user): array
    {
        return [
            'login_log_id' => $this->loginLog->id,
        ];
    }

    protected function formattedOrigins(): string
    {
        $origins = '';

        if (! is_null($this->loginLog->location)) {
            $origins = "<strong>Location:</strong> {$this->loginLog->location}<br>";
        }

        if (! is_null($this->loginLog->device)) {
            $origins .= "<strong>Device:</strong> {$this->loginLog->device}<br>";
        }

        if (! is_null($this->loginLog->browser)) {
            $origins .= "<strong>browser:</strong> {$this->loginLog->browser}<br>";
        }

        return $origins."<strong>IP Address:</strong> {$this->loginLog->ip}";
    }
}
