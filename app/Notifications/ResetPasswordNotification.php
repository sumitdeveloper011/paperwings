<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

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
        $settings = \Illuminate\Support\Facades\Cache::remember('email_settings', 3600, function() {
            return \App\Models\Setting::pluck('value', 'key')->toArray();
        });

        // Get social links from database
        $socialLinks = [];
        if (!empty($settings['social_facebook'])) {
            $socialLinks['facebook'] = $settings['social_facebook'];
        }
        if (!empty($settings['social_instagram'])) {
            $socialLinks['instagram'] = $settings['social_instagram'];
        }
        if (!empty($settings['social_twitter'])) {
            $socialLinks['twitter'] = $settings['social_twitter'];
        }
        if (!empty($settings['social_linkedin'])) {
            $socialLinks['linkedin'] = $settings['social_linkedin'];
        }

        // Get contact phone from database
        $contactPhone = null;
        if (isset($settings['phones']) && is_string($settings['phones'])) {
            $phones = json_decode($settings['phones'], true) ?? [];
            $contactPhone = !empty($phones) ? $phones[0] : null;
        } elseif (isset($settings['phones']) && is_array($settings['phones'])) {
            $contactPhone = !empty($settings['phones']) ? $settings['phones'][0] : null;
        }
        // Fallback if no phone found
        if (empty($contactPhone)) {
            $contactPhone = '+880 123 4567';
        }

        // Get contact email from database
        $contactEmail = null;
        if (isset($settings['emails']) && is_string($settings['emails'])) {
            $emails = json_decode($settings['emails'], true) ?? [];
            $contactEmail = !empty($emails) ? $emails[0] : null;
        } elseif (isset($settings['emails']) && is_array($settings['emails'])) {
            $contactEmail = !empty($settings['emails']) ? $settings['emails'][0] : null;
        }
        // Fallback if no email found
        if (empty($contactEmail)) {
            $contactEmail = 'info@paperwings.com';
        }

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
