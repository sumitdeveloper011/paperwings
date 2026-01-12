<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use App\Helpers\SettingHelper;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    // Get the notification's delivery channels
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // Get the mail representation of the notification
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

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
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails');
        // Fallback if no email found
        if (empty($contactEmail)) {
            $contactEmail = 'info@paperwings.com';
        }

        return (new MailMessage)
            ->subject('Verify Your Email Address - Paper Wings')
            ->view('emails.verify-email', [
                'verificationUrl' => $verificationUrl,
                'userName' => $notifiable->first_name,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ]);
    }
}
