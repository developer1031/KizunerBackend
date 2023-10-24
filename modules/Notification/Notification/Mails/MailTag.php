<?php

namespace Modules\Notification\Notification\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Modules\Config\Config;

class MailTag extends Notification implements ShouldQueue
{
    use Queueable;

    private $type;

    public function __construct($type)
    {
        $this->type = $type;
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
        $subject = '';
        $greeting = '';
        $line1 = '';

        if($this->type=='status') {
            $subject = 'You have been tagged to status';
            $greeting = 'Hello '. $notifiable->name . ',';
            $line1 = 'You are receiving this email because You have been tagged to status.';
        }
        else if($this->type=='hangout') {
            $subject = 'You have been tagged to Hangout';
            $greeting = 'Hello '. $notifiable->name . ',';
            $line1 = 'You are receiving this email because You have been tagged to Hangout.';
        }
        else if($this->type=='help') {
            $subject = 'You have been tagged to Help';
            $greeting = 'Hello '. $notifiable->name . ',';
            $line1 = 'You are receiving this email because You have been tagged to Help.';
        }
        else if($this->type=='up_level') {
            $subject = 'Congratulation!';
            $greeting = 'Hello '. $notifiable->name . ',';
            $line1 = 'You are receiving this email because You have reached new level.';
        }
        else if($this->type =='offline_remain') {
            $config_data = new Config();
            $offline_remain_setting =  json_decode($config_data->getConfig('offline_remain'), true);

            $subject = 'We are missing you!';
            $greeting = 'Hello '. $notifiable->name . ',';
            $line1 = 'You are receiving this email because You have reached new level.';
            if($offline_remain_setting) {
                if(isset($offline_remain_setting['content'])) {
                    $line1 = $offline_remain_setting['content'];
                }
            }
        }

        if($this->type) {
            return (new MailMessage)
                ->from('noreply@kizuner.com')
                ->subject($subject)
                ->greeting($greeting)
                ->line($line1)
                ->line('Thanks for using Kizuner.');
        }
    }
}
