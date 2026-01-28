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

class OrderShippedMail extends Mailable
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
        $template = $emailTemplateService->getTemplate('order_shipped');

        if (!$template) {
            throw new \Exception('Order shipped template not found in database');
        }

        $variables = [
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
            'app_name' => config('app.name'),
        ];
        $subject = $emailTemplateService->getSubject('order_shipped', $variables);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_shipped');

        if (!$template) {
            throw new \Exception('Order shipped template not found in database');
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

        $trackingNumber = $this->order->tracking_id ?? 'N/A';
        $trackingUrl = $this->order->tracking_url ?? '#';
        
        // Calculate estimated delivery date (default: 3-5 business days from now)
        $estimatedDeliveryDate = now()->addDays(5)->format('F j, Y');

        $variables = [
            'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
            'order_number' => $this->order->order_number,
            'tracking_number' => $trackingNumber,
            'tracking_url' => $trackingUrl,
            'estimated_delivery_date' => $estimatedDeliveryDate,
            'app_name' => config('app.name'),
        ];

        $body = $emailTemplateService->getBody('order_shipped', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'SHIPPING UPDATE',
                'headerTitle' => 'Your Order Has Shipped!',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }
}
