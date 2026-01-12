<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use App\Helpers\SettingHelper;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    // Create a new notification instance
    public function __construct($token)
    {
        $this->token = $token;
    }

    // Get the notification's delivery channels
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // Get the mail representation of the notification
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        // Fetch settings from database (same pattern as AppServiceProvider)
        $settings = SettingHelper::all();

        // Get social links from database
        $socialLinks = SettingHelper::extractSocialLinks($settings);

        // Get contact phone and email from database
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

        return (new MailMessage)
            ->subject('Reset Your Password - Paper Wings')
            ->view('emails.forgot-password', [
                'reset_password_link' => $resetUrl,
                'userName' => $notifiable->first_name,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ]);
    }

    // Get the array representation of the notification
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
