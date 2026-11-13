<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPassword extends Notification
{
    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('admin.password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);

        return (new MailMessage)
            ->subject('Reset your '.setting('site_name', config('app.name')).' dashboard password')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('We received a request to reset the password for your dashboard account.')
            ->action('Reset password', $url)
            ->line('This link expires in 60 minutes. If you did not request a reset, you can ignore this email.');
    }
}
