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
use Illuminate\Support\Facades\Cache;
use App\Helpers\SettingHelper;
use App\Services\EmailTemplateService;
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
                        if ($image) {
                            // Use thumbnail for emails (smaller file size, faster delivery)
                            $item->product->setAttribute('main_thumbnail_url', $image->thumbnail_url);
                            // Also set main_image_url for backward compatibility
                            $item->product->setAttribute('main_image_url', $image->image_url);
                        } else {
                            $item->product->setAttribute('main_thumbnail_url', asset('assets/images/placeholder.jpg'));
                            $item->product->setAttribute('main_image_url', asset('assets/images/placeholder.jpg'));
                        }
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
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_delivered');

        if ($template) {
            $variables = [
                'order_number' => $this->order->order_number,
                'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
                'app_name' => config('app.name'),
            ];
            $subject = $emailTemplateService->getSubject('order_delivered', $variables);
        } else {
            $subject = 'Order Delivered - Order #' . $this->order->order_number;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    // Get the message content definition
    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_delivered');

        if ($template) {
            $settings = SettingHelper::all();
            $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
            $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

            $variables = [
                'order_number' => $this->order->order_number,
                'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
                'app_name' => config('app.name'),
            ];

            $body = $emailTemplateService->getBody('order_delivered', $variables);

            return new Content(
                htmlString: $body,
            );
        }

        // Fallback to view if template doesn't exist
        $settings = SettingHelper::all();

        $logoUrl = url('assets/frontend/images/logo.png');
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = config('app.url') . '/assets/frontend/images/logo.png';
        }

        $socialLinks = SettingHelper::extractSocialLinks($settings);
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

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
        $settings = SettingHelper::all();

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
