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

    /**
     * The password reset token.
     *
     * @var string
     */
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
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

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
            ->subject('Reset Your Password - Paper Wings')
            ->view('emails.forgot-password', [
                'reset_password_link' => $resetUrl,
                'userName' => $notifiable->first_name,
                'logoUrl' => $logoUrl,
                'contactPhone' => config('app.contact_phone', '+11 111 333 4444'),
                'contactEmail' => config('app.contact_email', 'Info@YourCompany.com'),
                'socialLinks' => $socialLinks,
            ]);
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
