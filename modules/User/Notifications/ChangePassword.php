<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ChangePassword extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    public function __construct(string $pin)
    {
        $this->data = $pin;
    }

    /**
     * Get the notification's delivery channels.
     *
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from('noreply@kizuner.com')
            ->subject('Password Reset Email')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Your Pin is: ' . $this->data)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
