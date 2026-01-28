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
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('welcome_admin_user');

        if (!$template) {
            throw new \Exception('Welcome admin user template not found in database');
        }

        $settings = SettingHelper::all();
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+64 4-568 7770';
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.co.nz';
        $socialLinks = SettingHelper::extractSocialLinks($settings);

        // Get logo URL - prefer thumbnail for emails
        $logoUrl = url('assets/frontend/images/logo.png');
        $logo = SettingHelper::get('logo');
        if ($logo && !empty($logo)) {
            if (strpos($logo, '/original/') !== false) {
                $thumbnailPath = str_replace('/original/', '/thumbnails/', $logo);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath)) {
                    $logoUrl = asset('storage/' . $thumbnailPath);
                } else {
                    $mediumPath = str_replace('/original/', '/medium/', $logo);
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($mediumPath)) {
                        $logoUrl = asset('storage/' . $mediumPath);
                    } else {
                        $logoUrl = asset('storage/' . $logo);
                    }
                }
            } else {
                $pathParts = explode('/', $logo);
                $fileName = array_pop($pathParts);
                $basePath = implode('/', $pathParts);
                $thumbnailPath = $basePath . '/thumbnails/' . $fileName;
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath)) {
                    $logoUrl = asset('storage/' . $thumbnailPath);
                } else {
                    $logoUrl = asset('storage/' . $logo);
                }
            }
        }
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        // Get user roles
        $roles = $notifiable->roles->pluck('name')->implode(', ');

        $variables = [
            'admin_name' => $notifiable->name ?? $notifiable->first_name . ' ' . $notifiable->last_name,
            'admin_email' => $notifiable->email,
            'temporary_password' => $this->password,
            'admin_role' => $roles,
            'admin_login_url' => route('admin.login'),
            'app_name' => config('app.name'),
        ];

        $subject = $emailTemplateService->getSubject('welcome_admin_user', $variables);
        $body = $emailTemplateService->getBody('welcome_admin_user', $variables);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.template-body', [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'ADMIN ACCESS',
                'headerTitle' => 'Welcome to ' . config('app.name') . ' Admin Panel',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ]);
    }
}
