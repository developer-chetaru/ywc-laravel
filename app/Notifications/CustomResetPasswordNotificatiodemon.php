<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Instead of Laravel's default, load your custom Blade email
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->view('emails.custom-reset-password', [
                'url' => $resetUrl,
                'user' => $notifiable,
            ]);
    }
}
