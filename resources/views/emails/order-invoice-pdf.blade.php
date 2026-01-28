<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - #{{ $order->order_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000000;
            background-color: #ffffff;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #000000; background-color: #ffffff;">
    <table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff; max-width: 800px; margin: 0 auto;">
        <!-- Header Banner -->
        <tr>
            <td style="padding: 20px 30px; text-align: center; background-color: #2850a3;">
                @if(isset($logoUrl))
                <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 150px; height: auto; display: block; margin: 0 auto 15px auto;" />
                @endif
                <p style="margin: 0 0 8px 0; color: #ffffff; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                    ORDER CONFIRMATION
                </p>
                <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; line-height: 1.2;">
                    Thank You For Your Order!
                </h1>
            </td>
        </tr>
        
        <!-- Order Summary -->
        <tr>
            <td style="padding: 20px 30px; background-color: #ffffff;">
                <p style="margin: 0 0 10px 0; color: #000000; font-size: 13px; font-weight: 400;">
                    Hi {{ $order->billing_first_name }} {{ $order->billing_last_name }},
                </p>
                <p style="margin: 0 0 20px 0; color: #000000; font-size: 12px; font-weight: 400; line-height: 1.5;">
                    We've received your order and it's being processed. You'll receive a shipping confirmation email once your order ships.
                </p>
                
                <!-- Order Info Box -->
                <table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 6px; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 15px;">
                            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding-bottom: 10px;">
                                        <p style="margin: 0; color: #666666; font-size: 10px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Number
                                        </p>
                                        <p style="margin: 3px 0 0 0; color: #2850a3; font-size: 16px; font-weight: 700;">
                                            #{{ $order->order_number }}
                                        </p>
                                    </td>
                                    <td align="right" style="padding-bottom: 10px;">
                                        <p style="margin: 0; color: #666666; font-size: 10px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Date
                                        </p>
                                        <p style="margin: 3px 0 0 0; color: #000000; font-size: 16px; font-weight: 600;">
                                            {{ $order->created_at->format('F j, Y') }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 10px; border-top: 1px solid #e9ecef;">
                                        <p style="margin: 0; color: #666666; font-size: 10px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Order Status
                                        </p>
                                        <p style="margin: 3px 0 0 0; color: #e95c67; font-size: 14px; font-weight: 600;">
                                            {{ ucfirst($order->status ?? 'Pending') }}
                                        </p>
                                    </td>
                                    <td align="right" style="padding-top: 10px; border-top: 1px solid #e9ecef;">
                                        <p style="margin: 0; color: #666666; font-size: 10px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Payment Status
                                        </p>
                                        <p style="margin: 3px 0 0 0; color: #e95c67; font-size: 14px; font-weight: 600;">
                                            {{ ucfirst($order->payment_status ?? 'Pending') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Order Items -->
        <tr>
            <td style="padding: 0 30px 15px 30px; background-color: #ffffff;">
                <h2 style="margin: 0 0 12px 0; color: #2850a3; font-size: 16px; font-weight: 700; border-bottom: 2px solid #e9ecef; padding-bottom: 6px;">
                    Order Items
                </h2>
                
                <table cellspacing="0" cellpadding="0" border="0" width="100%" style="border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 8px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 1px solid #e9ecef; width: 70px;">Image</th>
                            <th style="padding: 8px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 1px solid #e9ecef;">Product</th>
                            <th style="padding: 8px; text-align: right; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 1px solid #e9ecef; width: 100px;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        @php
                            $productImage = asset('assets/images/placeholder.jpg');
                            if ($item->product && isset($item->product->main_thumbnail_url)) {
                                $productImage = $item->product->main_thumbnail_url;
                            } elseif ($item->product && isset($item->product->main_image_url)) {
                                $productImage = $item->product->main_image_url;
                            } elseif ($item->product && $item->product->images && $item->product->images->count() > 0) {
                                $productImage = $item->product->images->first()->thumbnail_url ?? $item->product->images->first()->image_url ?? asset('assets/images/placeholder.jpg');
                            }
                            $productName = $item->product_name ?? ($item->product->name ?? 'Product');
                            $productSku = $item->product->sku ?? ($item->product->barcode ?? ($item->product->eposnow_product_id ?? 'N/A'));
                            $productQuantity = $item->quantity ?? 1;
                            $productPrice = number_format($item->price ?? 0, 2);
                        @endphp
                        <tr>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: top;">
                                <img src="{{ $productImage }}" alt="{{ $productName }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; display: block;" />
                            </td>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: top;">
                                <p style="margin: 0 0 4px 0; color: #000000; font-size: 13px; font-weight: 600;">
                                    {{ $productName }}
                                </p>
                                <p style="margin: 0 0 2px 0; color: #666666; font-size: 11px; font-weight: 400;">
                                    SKU: {{ $productSku }}
                                </p>
                                <p style="margin: 0; color: #666666; font-size: 11px; font-weight: 400;">
                                    Quantity: {{ $productQuantity }}
                                </p>
                            </td>
                            <td style="padding: 10px 8px; border-bottom: 1px solid #e9ecef; vertical-align: top; text-align: right;">
                                <p style="margin: 0; color: #e95c67; font-size: 14px; font-weight: 700;">
                                    ${{ $productPrice }}
                                </p>
                            </td>
                        </tr>
                        @endforeach
                        <!-- Order Totals -->
                        
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 30px 15px 30px; background-color: #ffffff;">
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td width="100%" style="padding-right: 15px; vertical-align: top;">
                            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td width="50%">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            Subtotal:
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            ${{ number_format($order->subtotal ?? 0, 2) }}
                                        </p>
                                    </td>
                                </tr>
                                @if($order->discount && $order->discount > 0)
                                <tr>
                                    <td width="50%">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            Coupon:
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            ${{ number_format($order->discount, 2) }}
                                        </p>
                                    </td>
                                </tr>
                                @endif
                                @if($order->shipping && $order->shipping > 0)
                                <tr>
                                    <td width="50%">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            Shipping:
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            ${{ number_format($order->shipping, 2) }}
                                        </p>
                                    </td>
                                </tr>
                                @endif
                                @if($order->stripe_fee && $order->stripe_fee > 0)
                                <tr>
                                    <td width="50%">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            Stripe Fee:
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            ${{ number_format($order->stripe_fee, 2) }}
                                        </p>
                                    </td>
                                </tr>
                                @endif
                                @if($order->platform_fee && $order->platform_fee > 0)
                                <tr>
                                    <td width="50%">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            Platform Fee:
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            ${{ number_format($order->platform_fee, 2) }}
                                        </p>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td width="50%">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            Total:
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p
                                            style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                            ${{ number_format($order->total ?? 0, 2) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- Shipping & Billing Address -->
        <tr>
            <td style="padding: 0 30px 15px 30px; background-color: #ffffff;">
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td width="33%" style="padding-right: 15px; vertical-align: top;">
                            <h3
                                style="margin: 0 0 10px 0; color: #2850a3; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Payment Method
                            </h3>
                            <p style="margin: 0 0 5px 0; color: #000000; font-size: 12px; font-weight: 400;">
                                {{ ucfirst($order->payment_method ?? 'Credit Card') }}
                            </p>
                            @if($order->stripe_payment_method_id)
                            <p style="margin: 0; color: #000000; font-size: 12px; font-weight: 400;">
                                Card ending in {{ substr($order->stripe_payment_method_id, -4) }}
                            </p>
                            @endif
                        </td>
                        <td width="33%" style="padding-right: 15px; vertical-align: top;">
                            <h3 style="margin: 0 0 10px 0; color: #2850a3; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Shipping Address
                            </h3>
                            <p style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                                {{ $order->shipping_street_address ?? '' }}<br>
                                @if($order->shipping_suburb){{ $order->shipping_suburb }}<br>@endif
                                {{ $order->shipping_city }}, @if($order->shippingRegion){{ $order->shippingRegion->name }}@endif {{ $order->shipping_zip_code }}<br>
                                {{ $order->shipping_country }}
                            </p>
                        </td>
                        <td width="34%" style="padding-left: 15px; vertical-align: top;">
                            <h3 style="margin: 0 0 10px 0; color: #2850a3; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                Billing Address
                            </h3>
                            <p style="margin: 0; color: #000000; font-size: 11px; font-weight: 400; line-height: 1.5;">
                                {{ $order->billing_first_name }} {{ $order->billing_last_name }}<br>
                                {{ $order->billing_street_address ?? '' }}<br>
                                @if($order->billing_suburb){{ $order->billing_suburb }}<br>@endif
                                {{ $order->billing_city }}, @if($order->billingRegion){{ $order->billingRegion->name }}@endif {{ $order->billing_zip_code }}<br>
                                {{ $order->billing_country }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="padding: 15px 30px; background-color: #f5f5f5; border-top: 2px solid #e9ecef;">
                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="text-align: center;">
                            <p style="margin: 0 0 8px 0; color: #2850a3; font-size: 12px; font-weight: 600;">
                                Get in touch
                            </p>
                            @if(isset($contactPhone))
                            <p style="margin: 0 0 4px 0; color: #000000; font-size: 11px; font-weight: 400;">
                                {{ $contactPhone }}
                            </p>
                            @endif
                            @if(isset($contactEmail))
                            <p style="margin: 0 0 15px 0; color: #000000; font-size: 11px; font-weight: 400;">
                                {{ $contactEmail }}
                            </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Copyright Bar -->
        <tr>
            <td style="padding: 12px 30px; text-align: center; background-color: #2850a3;">
                <p style="margin: 0; color: #ffffff; font-size: 10px; font-weight: 400;">
                    Copyrights © {{ date('Y') }} {{ config('app.name') }} All Rights Reserved
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
