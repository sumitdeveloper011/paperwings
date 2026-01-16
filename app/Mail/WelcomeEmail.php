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

        if ($template) {
            $settings = SettingHelper::all();
            $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
            $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

            $variables = [
                'customer_name' => $this->user->name,
                'app_name' => config('app.name'),
                'login_url' => route('login'),
                'contact_phone' => $contactPhone,
                'contact_email' => $contactEmail,
            ];

            $body = $emailTemplateService->getBody('welcome_email', $variables);

            return new Content(
                view: 'emails.template',
                with: [
                    'body' => $body,
                    'logoUrl' => url('assets/frontend/images/logo.png'),
                    'contactPhone' => $contactPhone,
                    'contactEmail' => $contactEmail,
                    'socialLinks' => SettingHelper::extractSocialLinks($settings),
                ],
            );
        }

        $settings = SettingHelper::all();
        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        $socialLinks = SettingHelper::extractSocialLinks($settings);
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ],
        );
    }
}
