<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Helpers\SettingHelper;
use App\Services\ProductImageService;
use App\Services\EmailTemplateService;

class OrderProcessingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $order->load(['items.product.images', 'billingRegion', 'shippingRegion']);

        $productIds = $order->items->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            try {
                $images = ProductImageService::getFirstImagesForProducts($productIds);

                $order->items->each(function($item) use ($images) {
                    if ($item->product) {
                        $image = $images->get($item->product_id);
                        if ($image) {
                            $item->product->setAttribute('main_thumbnail_url', $image->thumbnail_url);
                            $item->product->setAttribute('main_image_url', $image->image_url);
                        } else {
                            $item->product->setAttribute('main_thumbnail_url', asset('assets/images/placeholder.jpg'));
                            $item->product->setAttribute('main_image_url', asset('assets/images/placeholder.jpg'));
                        }
                    }
                });
            } catch (\Exception $e) {
                $order->items->each(function($item) {
                    if ($item->product) {
                        $item->product->setAttribute('main_image_url', asset('assets/images/placeholder.jpg'));
                    }
                });
            }
        }

        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_processing');

        if ($template) {
            $variables = [
                'order_number' => $this->order->order_number,
                'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
                'order_total' => '$' . number_format($this->order->total, 2),
                'app_name' => config('app.name'),
            ];
            $subject = $emailTemplateService->getSubject('order_processing', $variables);
        } else {
            $subject = 'Order Processing - Order #' . $this->order->order_number;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_processing');

        if ($template) {
            $settings = SettingHelper::all();
            $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
            $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.com';

            $variables = [
                'order_number' => $this->order->order_number,
                'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
                'order_total' => '$' . number_format($this->order->total, 2),
                'app_name' => config('app.name'),
            ];

            $body = $emailTemplateService->getBody('order_processing', $variables);

            return new Content(
                html: $body,
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

        return new Content(
            view: 'emails.order-processing',
            with: [
                'order' => $this->order,
                'logoUrl' => $logoUrl,
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
            ],
        );
    }
}
