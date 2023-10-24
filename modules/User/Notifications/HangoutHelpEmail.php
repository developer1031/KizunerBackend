<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class HangoutHelpEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $name;

    private $subject;

    private $message;

    private $email;
    private $media;


    public function __construct(string $name,string $subject,string $message,string $email,string $media)
    {
        $this->name = $name;
        $this->subject = $subject;
        $this->message = $message;
        $this->email = $email;
        $this->media = $media;
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
        $mess = (new MailMessage)
            ->from($this->email)
            ->subject( $this->subject)
            ->line( $this->message);
            
        return $mess;
    }
}
