<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use App\Helpers\SettingHelper;
use App\Services\EmailTemplateService;

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

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('email_verification');

        if ($template) {
            $settings = SettingHelper::all();
            $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
            $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

            $variables = [
                'customer_name' => $notifiable->first_name,
                'verification_link' => $verificationUrl,
                'app_name' => config('app.name'),
            ];

            $subject = $emailTemplateService->getSubject('email_verification', $variables);
            $body = $emailTemplateService->getBody('email_verification', $variables);

            return (new MailMessage)
                ->subject($subject)
                ->html($body);
        }

        // Fallback to view if template doesn't exist
        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        $settings = SettingHelper::all();
        $socialLinks = SettingHelper::extractSocialLinks($settings);
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

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
