<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends ResetPassword
{
    use Queueable;

    /**
     * @var mixed
     */
    private $user;

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $this->user = $notifiable;
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {

        $message = "You are receiving this email because we received a password reset request for your account.
                          <br>
                          <b class='text-danger'> This password reset link will expire in ".config('auth.passwords.'.config('auth.defaults.passwords').'.expire')." minutes
                          </b><br>
                          If you did not create an account, no further action is required.";
        $data = [
            "buttonMessage" => "Reset Password",
            "url" => $url,
            "name" => $this->user->getNameAttribute(),
            "subject" => "Reset Password Notification",
            "content" => $message,
        ];
        return (new MailMessage) -> view( 'emails.infomail',$data);
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
