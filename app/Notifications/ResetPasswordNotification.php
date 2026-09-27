<?php
// app/Notifications/ResetPasswordNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $token;

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
        // URL del frontend con el token
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $resetUrl = $frontendUrl . '/reset-password?' . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $expira = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('🔐 Recuperación de contraseña - Next Level School')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Recibimos una solicitud para restablecer tu contraseña.')
            ->line('Haz clic en el siguiente botón para crear una nueva contraseña:')
            ->action('Restablecer contraseña', $resetUrl)
            ->line("⏰ Este enlace expirará en {$expira} minutos.")
            ->line('🔒 Si no solicitaste este cambio, ignora este mensaje. Tu contraseña actual seguirá siendo válida.')
            ->salutation('— Equipo de Next Level School');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'email' => $notifiable->email,
        ];
    }
}
