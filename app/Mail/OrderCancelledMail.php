<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use App\Helpers\SettingHelper;
use App\Services\EmailTemplateService;

class OrderCancelledMail extends Mailable
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
        $template = $emailTemplateService->getTemplate('order_cancelled');

        if (!$template) {
            throw new \Exception('Order cancelled template not found in database');
        }

        $variables = [
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
            'app_name' => config('app.name'),
        ];
        $subject = $emailTemplateService->getSubject('order_cancelled', $variables);

        return new Envelope(
            subject: $subject,
        );
    }

    // Get the message content definition
    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_cancelled');

        if (!$template) {
            throw new \Exception('Order cancelled template not found in database');
        }

        $settings = SettingHelper::all();
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';
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

        $orderStatus = ucfirst($this->order->status ?? 'Cancelled');

        // Build refund info row if refund exists
        $refundInfoRow = '';
        if ($this->order->refund_amount && $this->order->refund_amount > 0) {
            $refundInfo = 'Your refund of $' . number_format($this->order->refund_amount, 2) . ' has been processed.';
            if ($this->order->refund_reason) {
                $refundInfo .= ' Reason: ' . $this->order->refund_reason . '.';
            }
            if ($this->order->refunded_at) {
                $refundInfo .= ' The refund will appear in your account within 5-10 business days.';
            }
            
            $refundInfoRow = '<tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 10px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                ' . htmlspecialchars($refundInfo) . '
                            </p>
                        </td>
                    </tr>';
        }

        $variables = [
            'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
            'order_number' => $this->order->order_number,
            'order_status' => $orderStatus,
            'refund_info_row' => $refundInfoRow,
            'shop_url' => route('shop'),
            'app_name' => config('app.name'),
        ];

        $body = $emailTemplateService->getBody('order_cancelled', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'ORDER CANCELLATION',
                'headerTitle' => 'Your Order Has Been Cancelled',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }
}
