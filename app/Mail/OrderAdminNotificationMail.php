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

class OrderAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $order->load(['items.product.images', 'billingRegion', 'shippingRegion', 'user']);

        // Load product images efficiently
        $productIds = $order->items->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            try {
                $images = ProductImageService::getFirstImagesForProducts($productIds);

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

    public function envelope(): Envelope
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_admin_notification');

        if (!$template) {
            throw new \Exception('Order admin notification template not found in database');
        }

        $variables = [
            'order_number' => $this->order->order_number,
            'app_name' => config('app.name'),
        ];
        $subject = $emailTemplateService->getSubject('order_admin_notification', $variables);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $emailTemplateService = app(EmailTemplateService::class);
        $template = $emailTemplateService->getTemplate('order_admin_notification');

        if (!$template) {
            throw new \Exception('Order admin notification template not found in database');
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

        $orderItemsHtml = $this->buildOrderItemsHtml();
        $orderDate = $this->order->created_at->format('F j, Y');
        $orderStatus = ucfirst($this->order->status ?? 'Pending');
        $paymentStatus = ucfirst($this->order->payment_status ?? 'Pending');

        $customerName = $this->order->user 
            ? $this->order->user->name 
            : ($this->order->billing_first_name . ' ' . $this->order->billing_last_name);
        $customerEmail = $this->order->billing_email ?? ($this->order->user->email ?? '');
        
        $customerPhoneRow = '';
        $customerPhone = $this->order->billing_phone ?? ($this->order->user->phone ?? null);
        if ($customerPhone) {
            $customerPhoneRow = '<p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                <a href="tel:' . htmlspecialchars($customerPhone) . '" style="color: #2850a3; text-decoration: none;">' . htmlspecialchars($customerPhone) . '</a>
            </p>';
        }

        // Build conditional rows for order totals
        $couponDiscountRow = '';
        if ($this->order->discount && $this->order->discount > 0) {
            $couponDiscountRow = '<tr>
                <td style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                        Coupon
                    </p>
                </td>
                <td align="right" style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                        -$' . number_format($this->order->discount, 2) . '
                    </p>
                </td>
            </tr>';
        }

        $shippingCostRow = '';
        if ($this->order->shipping && $this->order->shipping > 0) {
            $shippingCostRow = '<tr>
                <td style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                        Shipping
                    </p>
                </td>
                <td align="right" style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                        $' . number_format($this->order->shipping, 2) . '
                    </p>
                </td>
            </tr>';
        }

        $stripeFeeRow = '';
        if ($this->order->stripe_fee && $this->order->stripe_fee > 0) {
            $stripeFeeRow = '<tr>
                <td style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                        Stripe Fee
                    </p>
                </td>
                <td align="right" style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                        $' . number_format($this->order->stripe_fee, 2) . '
                    </p>
                </td>
            </tr>';
        }

        $platformFeeRow = '';
        if ($this->order->platform_fee && $this->order->platform_fee > 0) {
            $platformFeeRow = '<tr>
                <td style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                        Platform Fee
                    </p>
                </td>
                <td align="right" style="padding-bottom: 10px;">
                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                        $' . number_format($this->order->platform_fee, 2) . '
                    </p>
                </td>
            </tr>';
        }

        $paymentCardInfo = '';
        if ($this->order->stripe_payment_method_id) {
            $paymentCardInfo = '<br>Card ending in ' . substr($this->order->stripe_payment_method_id, -4);
        }

        $shippingAddressLine2Break = !empty($this->order->shipping_suburb) ? ($this->order->shipping_suburb . '<br>') : '';
        $billingAddressLine2Break = !empty($this->order->billing_suburb) ? ($this->order->billing_suburb . '<br>') : '';

        $variables = [
            'order_number' => $this->order->order_number,
            'order_date' => $orderDate,
            'order_status' => $orderStatus,
            'payment_status' => $paymentStatus,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone_row' => $customerPhoneRow,
            'order_items' => $orderItemsHtml,
            'subtotal' => number_format($this->order->subtotal ?? 0, 2),
            'coupon_discount_row' => $couponDiscountRow,
            'shipping_cost_row' => $shippingCostRow,
            'stripe_fee_row' => $stripeFeeRow,
            'platform_fee_row' => $platformFeeRow,
            'total' => number_format($this->order->total ?? 0, 2),
            'shipping_name' => $this->order->shipping_first_name . ' ' . $this->order->shipping_last_name,
            'shipping_address_line1' => $this->order->shipping_street_address ?? '',
            'shipping_address_line2_break' => $shippingAddressLine2Break,
            'shipping_city' => $this->order->shipping_city ?? '',
            'shipping_state' => $this->order->shippingRegion->name ?? '',
            'shipping_zip' => $this->order->shipping_zip_code ?? '',
            'shipping_country' => $this->order->shipping_country ?? '',
            'billing_name' => $this->order->billing_first_name . ' ' . $this->order->billing_last_name,
            'billing_address_line1' => $this->order->billing_street_address ?? '',
            'billing_address_line2_break' => $billingAddressLine2Break,
            'billing_city' => $this->order->billing_city ?? '',
            'billing_state' => $this->order->billingRegion->name ?? '',
            'billing_zip' => $this->order->billing_zip_code ?? '',
            'billing_country' => $this->order->billing_country ?? '',
            'payment_method' => ucfirst($this->order->payment_method ?? 'Credit Card'),
            'payment_card_info' => $paymentCardInfo,
            'admin_order_view_url' => route('admin.orders.show', $this->order->uuid),
            'app_name' => config('app.name'),
        ];

        $body = $emailTemplateService->getBody('order_admin_notification', $variables);

        return new Content(
            view: 'emails.template-body',
            with: [
                'body' => $body,
                'logoUrl' => $logoUrl,
                'headerSubtitle' => 'NEW ORDER',
                'headerTitle' => 'New Order Received',
                'contactPhone' => $contactPhone,
                'contactEmail' => $contactEmail,
                'socialLinks' => $socialLinks,
                'currentYear' => date('Y'),
                'appName' => config('app.name'),
            ],
        );
    }

    private function buildOrderItemsHtml(): string
    {
        $html = '';
        $items = $this->order->items;

        foreach ($items as $item) {
            $product = $item->product;
            $productImage = $product->main_thumbnail_url ?? $product->main_image_url ?? asset('assets/images/placeholder.jpg');
            $productName = $item->product_name ?? ($product->name ?? 'Product');
            $productSku = $product->sku ?? 'N/A';
            $productQuantity = $item->quantity ?? 1;
            $productPrice = number_format($item->price ?? 0, 2);

            $html .= '<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px; border-bottom: 1px solid #e9ecef; padding-bottom: 20px;">
                <tr>
                    <td width="100" style="padding-right: 15px; vertical-align: top;">
                        <img src="' . $productImage . '" alt="' . htmlspecialchars($productName) . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; display: block;" />
                    </td>
                    <td style="vertical-align: top;">
                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 16px; font-weight: 600;">
                            ' . htmlspecialchars($productName) . '
                        </p>
                        <p style="margin: 0 0 8px 0; color: #666666; font-size: 14px; font-weight: 400;">
                            SKU: ' . htmlspecialchars($productSku) . '
                        </p>
                        <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                            Quantity: ' . $productQuantity . '
                        </p>
                    </td>
                    <td align="right" style="vertical-align: top;">
                        <p style="margin: 0; color: #e95c67; font-size: 18px; font-weight: 700;">
                            $' . $productPrice . '
                        </p>
                    </td>
                </tr>
            </table>';
        }

        return $html;
    }
}
