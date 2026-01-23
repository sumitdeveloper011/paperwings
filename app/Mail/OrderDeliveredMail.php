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

        if (!$template) {
            throw new \Exception('Order delivered template not found in database');
        }

        $variables = [
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
            'app_name' => config('app.name'),
        ];
        $subject = $emailTemplateService->getSubject('order_delivered', $variables);

        return new Envelope(
            subject: $subject,
        );
    }

    // Get the message content definition
    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_delivered');

        if (!$template) {
            throw new \Exception('Order delivered template not found in database');
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

        // Get delivery date (use updated_at when status changed to delivered, or current date)
        $deliveryDate = now()->format('F j, Y');
        
        // Generate review URL - link to order details page where they can review products
        $reviewUrl = route('account.order-details', $this->order->order_number);

        $variables = [
            'customer_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
            'order_number' => $this->order->order_number,
            'delivery_date' => $deliveryDate,
            'review_url' => $reviewUrl,
            'app_name' => config('app.name'),
        ];

        $body = $emailTemplateService->getBody('order_delivered', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'DELIVERY CONFIRMATION',
                'headerTitle' => 'Your Order Has Been Delivered!',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }

    // Get the attachments for the message
    public function attachments(): array
    {
        // Fetch settings from database for PDF
        $settings = SettingHelper::all();

        // Get logo URL - prefer thumbnail for PDF
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

        // Get contact phone from database
        $contactPhone = SettingHelper::getFirstFromArraySetting($settings, 'phones') ?? '+880 123 4567';

        // Get contact email from database
        $contactEmail = SettingHelper::getFirstFromArraySetting($settings, 'emails') ?? 'info@paperwings.co.nz';

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
