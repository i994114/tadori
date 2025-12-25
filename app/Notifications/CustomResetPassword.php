<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseReset;

class CustomResetPassword extends BaseReset
{
    public function toMail($notifiable)
    {
        $token = $this->token;
        $email = urlencode($notifiable->getEmailForPasswordReset());
        
        $resetUrl = url(route('password.reset', $token, false)) . "?email={$email}";

        $expiration = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('パスワード再設定のお知らせ')
            ->line('アカウントのパスワード再設定リクエストを受け取りました。下のボタンからパスワードを再設定してください。')
            ->action('パスワードを再設定する', $resetUrl)
            ->line("このリンクは{$expiration}分後に無効になります。")
            ->line('ご自身でのリクエストでなければ、このメールは破棄してください。');
    }
}
