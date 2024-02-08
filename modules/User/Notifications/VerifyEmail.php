<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends Notification implements ShouldQueue
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
            ->subject('Email Verification')
            ->line('This Email contain PIN code to help you verify your Email address.')
            ->line('Your Pin is: ' . $this->data)
            ->line('Please use this and enter in your pin verify step.');
    }
}
