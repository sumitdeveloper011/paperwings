<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Helpers\SettingHelper;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subject;
    public string $body;
    public string $email;
    public ?string $unsubscribeUrl;

    public function __construct(string $email, string $subject, string $body, ?string $unsubscribeToken = null)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
        $this->unsubscribeUrl = $unsubscribeToken 
            ? route('subscription.unsubscribe', $unsubscribeToken)
            : null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        $settings = SettingHelper::all();

        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        $contactEmail = null;
        if (isset($settings['emails']) && is_string($settings['emails'])) {
            $emails = json_decode($settings['emails'], true) ?? [];
            $contactEmail = !empty($emails) ? $emails[0] : null;
        } elseif (isset($settings['emails']) && is_array($settings['emails'])) {
            $contactEmail = !empty($settings['emails']) ? $settings['emails'][0] : null;
        }
        if (empty($contactEmail)) {
            $contactEmail = 'info@paperwings.com';
        }

        $socialLinks = [];
        if (!empty($settings['social_facebook'])) {
            $socialLinks['facebook'] = $settings['social_facebook'];
        }
        if (!empty($settings['social_instagram'])) {
            $socialLinks['instagram'] = $settings['social_instagram'];
        }
        if (!empty($settings['social_twitter'])) {
            $socialLinks['twitter'] = $settings['social_twitter'];
        }
        if (!empty($settings['social_linkedin'])) {
            $socialLinks['linkedin'] = $settings['social_linkedin'];
        }

        return new Content(
            view: 'emails.newsletter',
            with: [
                'body' => $this->body,
                'logoUrl' => $logoUrl,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ],
        );
    }
}
