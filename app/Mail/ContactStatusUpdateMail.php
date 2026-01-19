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
        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'solved' => 'Solved',
            'closed' => 'Closed',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('contact_status_update');

        if ($template) {
            $variables = [
                'customer_name' => $this->contactMessage->name,
                'message_subject' => $this->contactMessage->subject,
                'old_status' => $statusLabels[$this->oldStatus] ?? $this->oldStatus,
                'new_status' => $newStatusLabel,
                'app_name' => config('app.name'),
            ];
            $subject = $emailTemplateService->getSubject('contact_status_update', $variables);
        } else {
            $subject = 'Your Contact Message Status Updated - ' . config('app.name');
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('contact_status_update');

        $statusLabels = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'solved' => 'Solved',
            'closed' => 'Closed',
        ];

        if ($template) {
            $settings = SettingHelper::all();
            $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
            $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

            $logoUrl = url('assets/frontend/images/logo.png');
            if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
            }

            $adminNotes = '';
            if ($this->contactMessage->admin_notes) {
                $adminNotes = '<p style="margin: 30px 0 10px 0; color: #000000; font-size: 16px; font-weight: 600;">Admin Notes:</p>
                    <p style="margin: 0 0 15px 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6; white-space: pre-wrap;">' . htmlspecialchars($this->contactMessage->admin_notes) . '</p>';
            }

            $variables = [
                'customer_name' => $this->contactMessage->name,
                'message_subject' => $this->contactMessage->subject,
                'message_content' => $this->contactMessage->message,
                'old_status' => $statusLabels[$this->oldStatus] ?? $this->oldStatus,
                'new_status' => $statusLabels[$this->newStatus] ?? $this->newStatus,
                'app_name' => config('app.name'),
                'contact_phone' => $contactPhone,
                'contact_email' => $contactEmail,
                'logo_url' => $logoUrl,
                'current_year' => date('Y'),
                'admin_notes' => $adminNotes,
            ];

            $body = $emailTemplateService->getBody('contact_status_update', $variables);

            return new Content(
                html: $body,
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
            view: 'emails.contact-status-update',
            with: [
                'contactMessage' => $this->contactMessage,
                'oldStatus' => $statusLabels[$this->oldStatus] ?? $this->oldStatus,
                'newStatus' => $statusLabels[$this->newStatus] ?? $this->newStatus,
                'oldStatusValue' => $this->oldStatus,
                'newStatusValue' => $this->newStatus,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ],
        );
    }
}
