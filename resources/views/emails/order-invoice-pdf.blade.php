<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #000000; background-color: #ffffff;">
    <div style="max-width: 100%; margin: 0 auto; padding: 20px; background-color: #ffffff;">
        <!-- Header -->
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                @if(isset($logoUrl))
                <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 120px; height: auto; display: block;" />
                @endif
                <div style="text-align: right;">
                    <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; margin-bottom: 3px;">INVOICE</div>
                    <div style="font-size: 18px; font-weight: 700; color: #374E94;">#{{ $order->order_number }}</div>
                </div>
            </div>

            <div style="background-color: #374E94; color: #ffffff; padding: 15px 20px; border-radius: 6px;">
                <p style="margin: 0 0 5px 0; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9;">ORDER CONFIRMATION</p>
                <h1 style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff;">Thank You For Your Order!</h1>
            </div>
        </div>

        <!-- Order Information -->
        <div style="background-color: #f8f9fa; border-radius: 6px; padding: 15px; margin-bottom: 15px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; padding: 0; vertical-align: top;">
                        <div style="font-size: 9px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; margin-bottom: 3px;">Order Date</div>
                        <div style="font-size: 13px; font-weight: 600; color: #000000;">{{ $order->created_at->format('M d, Y') }}</div>
                    </td>
                    <td style="width: 50%; padding: 0; vertical-align: top; text-align: right;">
                        <div style="font-size: 9px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; margin-bottom: 3px;">Order Status</div>
                        <div style="font-size: 13px; font-weight: 700; color: #e95c67;">{{ ucfirst($order->status) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Order Items -->
        <h2 style="font-size: 14px; font-weight: 700; color: #374E94; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid #e9ecef;">Order Items</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 8px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef; width: 50px;">Image</th>
                    <th style="padding: 8px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef;">Product</th>
                    <th style="padding: 8px; text-align: center; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef; width: 60px;">Qty</th>
                    <th style="padding: 8px; text-align: right; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef; width: 80px;">Price</th>
                    <th style="padding: 8px; text-align: right; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef; width: 80px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: middle;">
                        @php
                            $productImage = asset('assets/images/placeholder.jpg');
                            // Prefer thumbnail for PDF (smaller file size, faster rendering)
                            if ($item->product && isset($item->product->main_thumbnail_url)) {
                                $productImage = $item->product->main_thumbnail_url;
                            } elseif ($item->product && isset($item->product->main_image_url)) {
                                $productImage = $item->product->main_image_url;
                            } elseif ($item->product && $item->product->images && $item->product->images->count() > 0) {
                                $productImage = $item->product->images->first()->thumbnail_url ?? $item->product->images->first()->image_url ?? asset('assets/images/placeholder.jpg');
                            }
                        @endphp
                        <img src="{{ $productImage }}" alt="{{ $item->product_name }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px; display: block;" />
                    </td>
                    <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: middle;">
                        <div style="font-size: 11px; font-weight: 600; color: #000000; margin-bottom: 3px;">{{ $item->product_name }}</div>
                        @if($item->product && $item->product->eposnow_product_id)
                        <div style="font-size: 9px; color: #666666;">SKU: {{ $item->product->eposnow_product_id }}</div>
                        @endif
                    </td>
                    <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: middle; text-align: center;">
                        <div style="font-size: 11px; color: #000000; font-weight: 600;">{{ $item->quantity }}</div>
                    </td>
                    <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: middle; text-align: right;">
                        <div style="font-size: 11px; color: #666666;">${{ number_format($item->price, 2) }}</div>
                    </td>
                    <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: middle; text-align: right;">
                        <div style="font-size: 11px; font-weight: 700; color: #e95c67;">${{ number_format($item->subtotal, 2) }}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Order Totals & Addresses Side by Side -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <!-- Order Totals -->
                <td style="width: 48%; padding: 0; padding-right: 15px; vertical-align: top;">
                    <div style="background-color: #f8f9fa; border-radius: 6px; padding: 15px;">
                        <h3 style="font-size: 12px; font-weight: 700; color: #374E94; margin-bottom: 10px; margin-top: 0; padding-bottom: 8px; border-bottom: 1px solid #e9ecef;">Order Summary</h3>
                        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                            <tr>
                                <td style="padding: 0; padding-bottom: 6px; color: #666666; font-weight: 400;">Subtotal</td>
                                <td style="padding: 0; padding-bottom: 6px; text-align: right; color: #000000; font-weight: 400;">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td style="padding: 0; padding-bottom: 6px; color: #666666; font-weight: 400;">Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</td>
                                <td style="padding: 0; padding-bottom: 6px; text-align: right; color: #e95c67; font-weight: 400;">-${{ number_format($order->discount, 2) }}</td>
                            </tr>
                            @endif
                            @if(($order->shipping_price ?? $order->shipping) > 0)
                            <tr>
                                <td style="padding: 0; padding-bottom: 6px; color: #666666; font-weight: 400;">Shipping</td>
                                <td style="padding: 0; padding-bottom: 6px; text-align: right; color: #000000; font-weight: 400;">${{ number_format($order->shipping_price ?? $order->shipping, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->tax > 0)
                            <tr>
                                <td style="padding: 0; padding-bottom: 6px; color: #666666; font-weight: 400;">Tax</td>
                                <td style="padding: 0; padding-bottom: 6px; text-align: right; color: #000000; font-weight: 400;">${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding: 0; padding-top: 8px; border-top: 2px solid #e9ecef; color: #374E94; font-size: 14px; font-weight: 700;">Total</td>
                                <td style="padding: 0; padding-top: 8px; border-top: 2px solid #e9ecef; text-align: right; color: #e95c67; font-size: 18px; font-weight: 700;">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
                <!-- Addresses -->
                <td style="width: 52%; padding: 0; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; padding: 0; padding-right: 10px; vertical-align: top;">
                                <h3 style="font-size: 10px; font-weight: 700; color: #374E94; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; margin-top: 0;">Shipping</h3>
                                <div style="font-size: 10px; color: #000000; line-height: 1.6;">
                                    {{ $order->shipping_full_name }}<br>
                                    {{ $order->shipping_street_address }}<br>
                                    @if($order->shipping_suburb){{ $order->shipping_suburb }}<br>@endif
                                    {{ $order->shipping_city }}, @if($order->shippingRegion){{ $order->shippingRegion->name }}@endif {{ $order->shipping_zip_code }}<br>
                                    {{ $order->shipping_country }}
                                </div>
                            </td>
                            <td style="width: 50%; padding: 0; padding-left: 10px; vertical-align: top;">
                                <h3 style="font-size: 10px; font-weight: 700; color: #374E94; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; margin-top: 0;">Billing</h3>
                                <div style="font-size: 10px; color: #000000; line-height: 1.6;">
                                    {{ $order->billing_full_name }}<br>
                                    {{ $order->billing_street_address }}<br>
                                    @if($order->billing_suburb){{ $order->billing_suburb }}<br>@endif
                                    {{ $order->billing_city }}, @if($order->billingRegion){{ $order->billingRegion->name }}@endif {{ $order->billing_zip_code }}<br>
                                    {{ $order->billing_country }}
                                </div>
                            </td>
                        </tr>
                    </table>
                    <!-- Payment Method -->
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e9ecef;">
                        <h3 style="font-size: 10px; font-weight: 700; color: #374E94; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; margin-top: 0;">Payment</h3>
                        <div style="font-size: 10px; color: #000000;">
                            {{ ucfirst($order->payment_method ?? 'N/A') }}
                            @if($order->payment_status)
                            <span style="color: #666666;"> • {{ ucfirst($order->payment_status) }}</span>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #e9ecef; text-align: center;">
            <div style="margin-bottom: 10px;">
                <div style="font-size: 11px; font-weight: 600; color: #374E94; margin-bottom: 5px;">Get in touch</div>
                @if(isset($contactPhone))
                <div style="font-size: 10px; color: #000000; margin-bottom: 3px;">{{ $contactPhone }}</div>
                @endif
                @if(isset($contactEmail))
                <div style="font-size: 10px; color: #000000; margin-bottom: 3px;">{{ $contactEmail }}</div>
                @endif
            </div>
            <div style="background-color: #374E94; color: #ffffff; padding: 10px; border-radius: 6px; font-size: 9px; margin-top: 10px;">
                Copyrights © {{ date('Y') }} PaperWings All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
