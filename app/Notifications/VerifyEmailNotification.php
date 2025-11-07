<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

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
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        // Get logo URL - using absolute URL for email
        $logoUrl = url('assets/images/logo.svg');
        // Ensure it's an absolute URL
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/images/logo.svg';
        }

        // Social media links (you can customize these in config or env)
        $socialLinks = [
            'facebook' => config('app.social_facebook', '#'),
            'instagram' => config('app.social_instagram', '#'),
            'twitter' => config('app.social_twitter', '#'),
            'linkedin' => config('app.social_linkedin', '#'),
        ];

        return (new MailMessage)
            ->subject('Verify Your Email Address - Paper Wings')
            ->view('emails.verify-email', [
                'verificationUrl' => $verificationUrl,
                'userName' => $notifiable->first_name,
                'logoUrl' => $logoUrl,
                'contactPhone' => config('app.contact_phone', '+880 123 4567'),
                'contactEmail' => config('app.contact_email', 'info@paperwings.com'),
                'socialLinks' => $socialLinks,
            ]);
    }
}
