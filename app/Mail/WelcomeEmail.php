<?php

namespace App\Mail;

use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Helpers\SettingHelper;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('welcome_email');

        if ($template) {
            $variables = [
                'customer_name' => $this->user->name,
                'app_name' => config('app.name'),
            ];
            $subject = $emailTemplateService->getSubject('welcome_email', $variables);
        } else {
            $subject = 'Welcome to ' . config('app.name') . '!';
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('welcome_email');

        if (!$template) {
            throw new \Exception('Welcome email template not found in database');
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

        $variables = [
            'logo_url' => $logoUrl,
            'user_name' => $this->user->name,
            'customer_name' => $this->user->name,
            'app_name' => config('app.name'),
            'shop_link' => route('shop'),
            'login_url' => route('login'),
            'contact_phone' => $contactPhone,
            'contact_email' => $contactEmail,
            'social_facebook' => $socialLinks['facebook'] ?? '#',
            'social_instagram' => $socialLinks['instagram'] ?? '#',
            'social_twitter' => $socialLinks['twitter'] ?? '#',
            'social_linkedin' => $socialLinks['linkedin'] ?? '#',
            'current_year' => date('Y'),
        ];

        $body = $emailTemplateService->getBody('welcome_email', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'WELCOME ABOARD!',
                'headerTitle' => 'Welcome to ' . config('app.name'),
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }
}
