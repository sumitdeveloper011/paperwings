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

class ContactStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactMessage;
    public $oldStatus;
    public $newStatus;

    public function __construct(ContactMessage $contactMessage, string $oldStatus, string $newStatus)
    {
        $this->contactMessage = $contactMessage;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function envelope(): Envelope
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('contact_status_update');

        if (!$template) {
            throw new \Exception('Contact status update template not found in database');
        }

        $variables = [
            'contact_name' => $this->contactMessage->name,
            'app_name' => config('app.name'),
        ];
        $subject = $emailTemplateService->getSubject('contact_status_update', $variables);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('contact_status_update');

        if (!$template) {
            throw new \Exception('Contact status update template not found in database');
        }

        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'solved' => 'Solved',
            'closed' => 'Closed',
        ];

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
        
        // Get status label
        $statusLabel = $statusLabels[$this->newStatus] ?? ucfirst(str_replace('_', ' ', $this->newStatus));

        $variables = [
            'contact_name' => $this->contactMessage->name,
            'reference_number' => $referenceNumber,
            'status' => $statusLabel,
            'app_name' => config('app.name'),
        ];

        $body = $emailTemplateService->getBody('contact_status_update', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'STATUS UPDATE',
                'headerTitle' => 'Your Contact Message Status Updated',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }
}
