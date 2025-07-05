<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $verificationUrl;

    public function __construct(User $user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verifikasi Email Anda - Wipa Motor')
            ->view('emails.verify-email', [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
} 