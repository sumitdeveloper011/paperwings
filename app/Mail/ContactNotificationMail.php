<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Helpers\SettingHelper;

class ContactNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactMessage;

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    public function envelope(): Envelope
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('contact_notification');

        if ($template) {
            $variables = [
                'customer_name' => $this->contactMessage->name,
                'message_subject' => $this->contactMessage->subject,
                'message_content' => $this->contactMessage->message,
                'app_name' => config('app.name'),
            ];
            $subject = $emailTemplateService->getSubject('contact_notification', $variables);
        } else {
            $subject = 'Thank You for Contacting Us - ' . config('app.name');
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('contact_notification');

        if ($template) {
            $settings = SettingHelper::all();
            $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
            $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

            $variables = [
                'customer_name' => $this->contactMessage->name,
                'message_subject' => $this->contactMessage->subject,
                'message_content' => $this->contactMessage->message,
                'app_name' => config('app.name'),
                'contact_phone' => $contactPhone,
                'contact_email' => $contactEmail,
            ];

            $body = $emailTemplateService->getBody('contact_notification', $variables);

            return new Content(
                htmlString: $body,
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
            view: 'emails.contact-notification',
            with: [
                'contactMessage' => $this->contactMessage,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ],
        );
    }
}
