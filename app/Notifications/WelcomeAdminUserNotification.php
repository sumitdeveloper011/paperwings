<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use App\Helpers\SettingHelper;

class WelcomeAdminUserNotification extends Notification
{
    use Queueable;

    public $password;

    // Create a new notification instance
    public function __construct($password)
    {
        $this->password = $password;
    }

    // Get the notification's delivery channels
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // Get the mail representation of the notification
    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = route('admin.login');

        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        // Fetch settings from database
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

        // Get user roles
        $roles = $notifiable->roles->pluck('name')->implode(', ');

        return (new MailMessage)
            ->subject('Welcome to Paper Wings Admin Panel')
            ->view('emails.welcome-admin-user', [
                'userName' => $notifiable->first_name,
                'email' => $notifiable->email,
                'password' => $this->password,
                'loginUrl' => $loginUrl,
                'roles' => $roles,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ]);
    }
}
