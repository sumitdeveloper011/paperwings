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

        if (!$template) {
            throw new \Exception('Contact notification template not found in database');
        }

        $variables = [
            'contact_name' => $this->contactMessage->name,
            'app_name' => config('app.name'),
        ];
        $subject = $emailTemplateService->getSubject('contact_notification', $variables);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('contact_notification');

        if (!$template) {
            throw new \Exception('Contact notification template not found in database');
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

        // Format reference number
        $referenceNumber = 'CONT-' . strtoupper(substr($this->contactMessage->uuid, 0, 8)) . '-' . $this->contactMessage->id;
        
        // Get message preview (first 150 characters)
        $messagePreview = mb_substr(strip_tags($this->contactMessage->message), 0, 150);
        if (mb_strlen($this->contactMessage->message) > 150) {
            $messagePreview .= '...';
        }

        $variables = [
            'contact_name' => $this->contactMessage->name,
            'message_preview' => $messagePreview,
            'reference_number' => $referenceNumber,
            'response_time' => '24-48 hours',
            'app_name' => config('app.name'),
        ];

        $body = $emailTemplateService->getBody('contact_notification', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'CONTACT FORM SUBMISSION',
                'headerTitle' => 'Thank You for Contacting Us',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }
}
