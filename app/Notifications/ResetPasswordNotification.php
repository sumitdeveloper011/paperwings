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

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('password_reset');

        if ($template) {
            $settings = SettingHelper::all();
            $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
            $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

            $variables = [
                'customer_name' => $notifiable->first_name,
                'reset_link' => $resetUrl,
                'expiry_time' => '60 minutes',
                'app_name' => config('app.name'),
            ];

            $subject = $emailTemplateService->getSubject('password_reset', $variables);
            $body = $emailTemplateService->getBody('password_reset', $variables);

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
