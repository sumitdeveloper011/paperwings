<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    // Create a new message instance
    public function __construct(Order $order)
    {
        $order->load(['items.product', 'billingRegion', 'shippingRegion']);
        $this->order = $order;
    }

    // Get the message envelope
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - Order #' . $this->order->order_number,
        );
    }

    // Get the message content definition
    public function content(): Content
    {
        // Fetch settings from database (same pattern as AppServiceProvider)
        $settings = \Illuminate\Support\Facades\Cache::remember('email_settings', 3600, function() {
            return \App\Models\Setting::pluck('value', 'key')->toArray();
        });

        // Get logo URL
        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        // Get social links from database
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

        // Get contact phone from database
        $contactPhone = null;
        if (isset($settings['phones']) && is_string($settings['phones'])) {
            $phones = json_decode($settings['phones'], true) ?? [];
            $contactPhone = !empty($phones) ? $phones[0] : null;
        } elseif (isset($settings['phones']) && is_array($settings['phones'])) {
            $contactPhone = !empty($settings['phones']) ? $settings['phones'][0] : null;
        }
        if (empty($contactPhone)) {
            $contactPhone = '+880 123 4567';
        }

        // Get contact email from database
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

        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order' => $this->order,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ],
        );
    }

    // Get the attachments for the message
    public function attachments(): array
    {
        $pdf = Pdf::loadView('emails.order-invoice-pdf', ['order' => $this->order]);
        $pdfPath = storage_path('app/temp/invoice_' . $this->order->order_number . '_' . time() . '.pdf');

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $pdf->save($pdfPath);

        register_shutdown_function(function() use ($pdfPath) {
            if (file_exists($pdfPath)) {
                @unlink($pdfPath);
            }
        });

        return [
            Attachment::fromPath($pdfPath)
                ->as('Invoice_' . $this->order->order_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
