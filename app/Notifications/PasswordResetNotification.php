<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
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
        $message = "You are receiving this email because you recently reset your passowrd on your account.
                          <br>
                          <b class='text-danger'>
                          If you did not request this action on your account, Please reset your password .</b>
                            <a href='".route('password.request')."' role='button' class='btn btn-lg bg-gradient-danger btn-lg w-100 mt-4 mb-0'> Reset Password</a>";
        $data = [
            "url" => null,
            "name" => $notifiable->getNameAttribute(),
            "subject" => "Password Reset Successful",
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
