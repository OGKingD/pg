<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        //
        $this->user = $user;
    }


    public function buildMailMessage($url)
    {
        $data = [
            "buttonMessage" => "Verify Email Address",
            "url" => $url,
            "name" => $this->user->getNameAttribute(),
            "subject" => "Verify Email Address",
            "content" => "Please click the button below to verify your email address. <br> <br> If you did not create an account, no further action is required.",
            ];
        return (new MailMessage) -> view( 'emails.infomail',$data);

    }


}
