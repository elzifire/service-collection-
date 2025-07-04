<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $urlApp = sprintf(
            'masjiduika://auth/reset-password?token=%s&email=%s',
            $this->token,
            urlencode($notifiable->email)
        );

        $urlWeb = sprintf(
            'https://masjid.uika-bogor.ac.id/service/reset-password?token=%s&email=%s',
            $this->token,
            urlencode($notifiable->email)
        );

        Log::info('Reset Password Notification: Generating URL', [
            'url_web' => $urlWeb,
            'url_app' => $urlApp
        ]);

        return (new MailMessage)
            ->subject('Reset Password')
            ->greeting('Halo!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset via Aplikasi', $urlApp)
            ->line('Atau jika Anda membuka email ini di browser, silakan klik tombol berikut:')
            ->action('Reset via Website', $urlWeb)
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
            ->salutation('Terima kasih, Tim Masjid UIKA');
    }
}
