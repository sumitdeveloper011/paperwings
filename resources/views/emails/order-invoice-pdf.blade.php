<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - #{{ $order->order_number }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #000000; background-color: #ffffff;">
    <div style="max-width: 800px; margin: 0 auto; padding: 40px; background-color: #ffffff;">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 40px;">
            @if(isset($logoUrl))
            <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 180px; height: auto; margin-bottom: 30px; display: block; margin-left: auto; margin-right: auto;" />
            @endif
            
            <div style="background-color: #374E94; color: #ffffff; padding: 40px 30px; border-radius: 8px; margin-bottom: 30px;">
                <p style="margin: 0 0 10px 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">ORDER CONFIRMATION</p>
                <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #ffffff;">Thank You For Your Order!</h1>
            </div>
        </div>

        <!-- Order Information -->
        <div style="background-color: #f8f9fa; border-radius: 8px; padding: 25px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <div style="font-size: 11px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; margin-bottom: 5px;">Order Number</div>
                    <div style="font-size: 18px; font-weight: 700; color: #374E94;">#{{ $order->order_number }}</div>
                </div>
                <div>
                    <div style="font-size: 11px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; margin-bottom: 5px;">Order Date</div>
                    <div style="font-size: 18px; font-weight: 600; color: #000000;">{{ $order->created_at->format('F d, Y') }}</div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 15px; border-top: 1px solid #e9ecef;">
                <div>
                    <div style="font-size: 11px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; margin-bottom: 5px;">Order Status</div>
                    <div style="font-size: 16px; font-weight: 700; color: #e95c67;">{{ ucfirst($order->status) }}</div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <h2 style="font-size: 18px; font-weight: 700; color: #374E94; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">Order Items</h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef; width: 100px;">Image</th>
                    <th style="padding: 15px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef;">Product</th>
                    <th style="padding: 15px; text-align: right; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #666666; border-bottom: 2px solid #e9ecef; width: 120px;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 20px 15px; border-bottom: 1px solid #e9ecef; vertical-align: top;">
                        @php
                            $productImage = asset('assets/images/placeholder.jpg');
                            if ($item->product && isset($item->product->main_image_url)) {
                                $productImage = $item->product->main_image_url;
                            } elseif ($item->product && $item->product->images && $item->product->images->count() > 0) {
                                $productImage = $item->product->images->first()->image_url ?? asset('assets/images/placeholder.jpg');
                            }
                        @endphp
                        <img src="{{ $productImage }}" alt="{{ $item->product_name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; display: block;" />
                    </td>
                    <td style="padding: 20px 15px; border-bottom: 1px solid #e9ecef; vertical-align: top; padding-left: 15px;">
                        <div style="font-size: 16px; font-weight: 600; color: #000000; margin-bottom: 5px;">{{ $item->product_name }}</div>
                        @if($item->product && $item->product->eposnow_product_id)
                        <div style="font-size: 12px; color: #666666; margin-bottom: 3px;">SKU: {{ $item->product->eposnow_product_id }}</div>
                        @endif
                        <div style="font-size: 12px; color: #666666;">Quantity: {{ $item->quantity }}</div>
                    </td>
                    <td style="padding: 20px 15px; border-bottom: 1px solid #e9ecef; vertical-align: top; text-align: right;">
                        <div style="font-size: 16px; font-weight: 700; color: #e95c67;">${{ number_format($item->subtotal, 2) }}</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Order Totals -->
        <div style="background-color: #f8f9fa; border-radius: 8px; padding: 25px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #666666; font-weight: 400;">Subtotal</span>
                <span style="color: #000000; font-weight: 400;">${{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #666666; font-weight: 400;">Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</span>
                <span style="color: #000000; font-weight: 400;">-${{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            @if(($order->shipping_price ?? $order->shipping) > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #666666; font-weight: 400;">Shipping</span>
                <span style="color: #000000; font-weight: 400;">${{ number_format($order->shipping_price ?? $order->shipping, 2) }}</span>
            </div>
            @endif
            @if($order->tax > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #666666; font-weight: 400;">Tax</span>
                <span style="color: #000000; font-weight: 400;">${{ number_format($order->tax, 2) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding-top: 15px; margin-top: 15px; border-top: 2px solid #e9ecef;">
                <span style="color: #374E94; font-size: 18px; font-weight: 700;">Total</span>
                <span style="color: #e95c67; font-size: 24px; font-weight: 700;">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Shipping & Billing Address -->
        <div style="display: flex; gap: 30px; margin-bottom: 30px;">
            <div style="flex: 1;">
                <h3 style="font-size: 14px; font-weight: 700; color: #374E94; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px; margin-top: 0;">Shipping Address</h3>
                <div style="font-size: 14px; color: #000000; line-height: 1.8;">
                    {{ $order->shipping_full_name }}<br>
                    {{ $order->shipping_street_address }}<br>
                    @if($order->shipping_suburb){{ $order->shipping_suburb }}<br>@endif
                    {{ $order->shipping_city }}, @if($order->shippingRegion){{ $order->shippingRegion->name }}@endif {{ $order->shipping_zip_code }}<br>
                    {{ $order->shipping_country }}
                </div>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 14px; font-weight: 700; color: #374E94; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px; margin-top: 0;">Billing Address</h3>
                <div style="font-size: 14px; color: #000000; line-height: 1.8;">
                    {{ $order->billing_full_name }}<br>
                    {{ $order->billing_street_address }}<br>
                    @if($order->billing_suburb){{ $order->billing_suburb }}<br>@endif
                    {{ $order->billing_city }}, @if($order->billingRegion){{ $order->billingRegion->name }}@endif {{ $order->billing_zip_code }}<br>
                    {{ $order->billing_country }}
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 14px; font-weight: 700; color: #374E94; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px; margin-top: 0;">Payment Method</h3>
            <div style="font-size: 14px; color: #000000; line-height: 1.8;">
                {{ ucfirst($order->payment_method ?? 'N/A') }}<br>
                @if($order->payment_status)
                Status: {{ ucfirst($order->payment_status) }}
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 50px; padding-top: 30px; border-top: 2px solid #e9ecef; text-align: center;">
            <div style="margin-bottom: 20px;">
                <div style="font-size: 16px; font-weight: 600; color: #374E94; margin-bottom: 10px;">Get in touch</div>
                @if(isset($contactPhone))
                <div style="font-size: 14px; color: #000000; margin-bottom: 5px;">{{ $contactPhone }}</div>
                @endif
                @if(isset($contactEmail))
                <div style="font-size: 14px; color: #000000; margin-bottom: 5px;">{{ $contactEmail }}</div>
                @endif
            </div>
            <div style="background-color: #374E94; color: #ffffff; padding: 15px; border-radius: 8px; font-size: 12px; margin-top: 20px;">
                Copyrights © {{ date('Y') }} PaperWings All Rights Reserved
            </div>
        </div>
    </div>
</body>
</html>
