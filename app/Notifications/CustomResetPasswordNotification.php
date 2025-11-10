<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;


class CustomResetPasswordNotification extends Notification
{
    use Queueable;
    public $token;
    public $orgName;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $orgName)
    {
        $this->token = $token;
        $this->orgName = $orgName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        return (new MailMessage)
            ->subject("{$this->orgName} - Reset Your Password")
            ->view('emails.custom-reset-password', [
                'user'        => $notifiable,   // jise invite kiya
                'orgName'     => $this->orgName,
                'resetUrl'    => $resetUrl,
                'userFullName'=> $notifiable->first_name . ' ' . $notifiable->last_name,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
