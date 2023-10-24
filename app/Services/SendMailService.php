<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class SendMailService
{
    public static function sendMail($view, $to, $subject, $data) {
        Mail::send(
            $view,
            $data,
            function ($mail) use ($to, $subject, $data) {
                $mail->from($data['from'], $data['sender'])
                    ->to($to, !empty($data['full_name']) ? $data['full_name'] : '')
                    //->bcc($data['cc'], $data['cc_sender'])
                    //->bcc('bxthuan@gmail.com')
                    ->subject($subject);
            }
        );
    }
}
