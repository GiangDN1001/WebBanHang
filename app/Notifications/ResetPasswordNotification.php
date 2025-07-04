<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;
    public $token;
    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Yêu cầu khôi phục mật khẩu')
            ->greeting('Xin chào!')
            ->line('Bạn vừa yêu cầu khôi phục mật khẩu cho tài khoản tại QCTech Việt Nam.')
            ->action('Đặt lại mật khẩu ngay', $url)
            ->line('Liên kết sẽ hết hạn sau 60 phút.')
            ->line('Nếu bạn không yêu cầu, hãy bỏ qua email này.')
            ->salutation('Trân trọng, QCTech Việt Nam');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
