<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h1 {
            color: #2c3e50;
            margin: 10px 0;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
        }
        .info-value {
            color: #2c3e50;
        }
        .order-items {
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-name {
            font-weight: 600;
            color: #2c3e50;
        }
        .item-details {
            color: #6c757d;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .totals {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 1.1em;
        }
        .total-row--final {
            font-size: 1.3em;
            font-weight: bold;
            color: #e74c3c;
            border-top: 2px solid #e9ecef;
            padding-top: 15px;
            margin-top: 10px;
        }
        .address-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .address-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 0.9em;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2c3e50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Common Header -->
        <div class="header" style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e9ecef;">
            @if(isset($logoUrl))
            <img src="{{ $logoUrl }}" alt="Company Logo" class="logo" style="max-width: 150px; margin-bottom: 10px;" />
            @endif
            <h1 style="color: #2c3e50; margin: 10px 0;">Order Confirmation</h1>
            <p>Thank you for your order!</p>
        </div>

        <div class="order-info">
            <div class="info-row">
                <span class="info-label">Order Number:</span>
                <span class="info-value">{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value">{{ $order->created_at->format('F d, Y h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="info-value" style="text-transform: capitalize;">{{ $order->payment_status }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Status:</span>
                <span class="info-value" style="text-transform: capitalize;">{{ $order->status }}</span>
            </div>
        </div>

        <div class="order-items">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Order Items</h2>
            @foreach($order->items as $item)
            <div class="order-item">
                <div>
                    <div class="item-name">{{ $item->product_name }}</div>
                    <div class="item-details">Quantity: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</div>
                </div>
                <div style="font-weight: 600; color: #2c3e50;">
                    ${{ number_format($item->subtotal, 2) }}
                </div>
            </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount > 0)
            <div class="total-row">
                <span>Discount ({{ $order->coupon_code }}):</span>
                <span style="color: #e74c3c;">-${{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            @if($order->shipping > 0 || $order->shipping_price > 0)
            <div class="total-row">
                <span>Shipping:</span>
                <span>${{ number_format($order->shipping_price ?? $order->shipping, 2) }}</span>
            </div>
            @endif
            <div class="total-row total-row--final">
                <span>Total:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div style="display: flex; gap: 20px; margin: 20px 0;">
            <div class="address-section" style="flex: 1;">
                <div class="address-title">Billing Address</div>
                <div>
                    {{ $order->billing_full_name }}<br>
                    {{ $order->billing_street_address }}<br>
                    @if($order->billing_suburb){{ $order->billing_suburb }}, @endif
                    {{ $order->billing_city }}<br>
                    @if($order->billingRegion){{ $order->billingRegion->name }}, @endif
                    {{ $order->billing_zip_code }}<br>
                    {{ $order->billing_country }}<br>
                    <strong>Email:</strong> {{ $order->billing_email }}<br>
                    <strong>Phone:</strong> {{ $order->billing_phone }}
                </div>
            </div>

            <div class="address-section" style="flex: 1;">
                <div class="address-title">Shipping Address</div>
                <div>
                    {{ $order->shipping_full_name }}<br>
                    {{ $order->shipping_street_address }}<br>
                    @if($order->shipping_suburb){{ $order->shipping_suburb }}, @endif
                    {{ $order->shipping_city }}<br>
                    @if($order->shippingRegion){{ $order->shippingRegion->name }}, @endif
                    {{ $order->shipping_zip_code }}<br>
                    {{ $order->shipping_country }}<br>
                    <strong>Email:</strong> {{ $order->shipping_email }}<br>
                    <strong>Phone:</strong> {{ $order->shipping_phone }}
                </div>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p style="color: #6c757d;">A detailed invoice has been attached to this email.</p>
        </div>

        <div class="footer" style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e9ecef; color: #6c757d; font-size: 0.9em;">
            <p>If you have any questions about your order, please contact us.</p>
            <p>Thank you for shopping with us!</p>
            
            <!-- Contact Information -->
            @if(isset($contactPhone) || isset($contactEmail))
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p style="margin: 0 0 8px 0; color: #000000; font-size: 14px; font-weight: 400;">
                    @if(isset($contactPhone)){{ $contactPhone }}@endif
                </p>
                <p style="margin: 0 0 15px 0; color: #000000; font-size: 14px; font-weight: 400;">
                    @if(isset($contactEmail)){{ $contactEmail }}@endif
                </p>
                
                <!-- Social Media Icons -->
                @if(isset($socialLinks) && !empty($socialLinks))
                <div style="margin-top: 15px;">
                    @if(isset($socialLinks['facebook']) && !empty($socialLinks['facebook']))
                    <a href="{{ $socialLinks['facebook'] }}" style="display: inline-block; width: 32px; height: 32px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none; margin: 0 4px;">
                        <span style="color: #ffffff; font-size: 14px; font-weight: 700;">f</span>
                    </a>
                    @endif
                    @if(isset($socialLinks['instagram']) && !empty($socialLinks['instagram']))
                    <a href="{{ $socialLinks['instagram'] }}" style="display: inline-block; width: 32px; height: 32px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none; margin: 0 4px;">
                        <span style="color: #ffffff; font-size: 14px; font-weight: 700;">i</span>
                    </a>
                    @endif
                    @if(isset($socialLinks['twitter']) && !empty($socialLinks['twitter']))
                    <a href="{{ $socialLinks['twitter'] }}" style="display: inline-block; width: 32px; height: 32px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none; margin: 0 4px;">
                        <span style="color: #ffffff; font-size: 12px; font-weight: 700;">t</span>
                    </a>
                    @endif
                    @if(isset($socialLinks['linkedin']) && !empty($socialLinks['linkedin']))
                    <a href="{{ $socialLinks['linkedin'] }}" style="display: inline-block; width: 32px; height: 32px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none; margin: 0 4px;">
                        <span style="color: #ffffff; font-size: 10px; font-weight: 700;">in</span>
                    </a>
                    @endif
                </div>
                @endif
            </div>
            @endif
            
            <!-- Copyright -->
            <p style="margin-top: 20px; color: #6c757d; font-size: 12px;">
                Copyrights © {{ date('Y') }} Paper Wings All Rights Reserved
            </p>
        </div>
    </div>
</body>
</html>
