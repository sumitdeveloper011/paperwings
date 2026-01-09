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

class OrderDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    // Create a new message instance
    public function __construct(Order $order)
    {
        $order->load(['items.product.images', 'billingRegion', 'shippingRegion']);

        // Load product images efficiently
        $productIds = $order->items->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            try {
                $images = \App\Services\ProductImageService::getFirstImagesForProducts($productIds);

                $order->items->each(function($item) use ($images) {
                    if ($item->product) {
                        $image = $images->get($item->product_id);
                        $item->product->setAttribute('main_image_url',
                            $image && isset($image->image_url) ? $image->image_url : asset('assets/images/placeholder.jpg')
                        );
                    }
                });
            } catch (\Exception $e) {
                // If image loading fails, set placeholder for all items
                $order->items->each(function($item) {
                    if ($item->product) {
                        $item->product->setAttribute('main_image_url', asset('assets/images/placeholder.jpg'));
                    }
                });
            }
        }

        $this->order = $order;
    }

    // Get the message envelope
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Delivered - Order #' . $this->order->order_number,
        );
    }

    // Get the message content definition
    public function content(): Content
    {
        // Fetch settings from database
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

        // Get order view URL
        $orderViewUrl = route('account.order-details', $this->order->order_number);

        return new Content(
            view: 'emails.order-delivered',
            with: [
                'order' => $this->order,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'orderViewUrl' => $orderViewUrl,
            ],
        );
    }

    // Get the attachments for the message
    public function attachments(): array
    {
        // Fetch settings from database for PDF
        $settings = \Illuminate\Support\Facades\Cache::remember('email_settings', 3600, function() {
            return \App\Models\Setting::pluck('value', 'key')->toArray();
        });

        // Get logo URL
        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
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
            $contactPhone = '+11 111 333 4444';
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
            $contactEmail = 'Info@YourCompany.com';
        }

        $pdf = Pdf::loadView('emails.order-invoice-pdf', [
            'order' => $this->order,
            'logoUrl' => $logoUrl,
            'contactPhone' => $contactPhone,
            'contactEmail' => $contactEmail,
        ])->setPaper('a4', 'portrait')->setOption('margin-top', 15)
          ->setOption('margin-bottom', 15)
          ->setOption('margin-left', 15)
          ->setOption('margin-right', 15);
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
