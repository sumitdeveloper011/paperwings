<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-info-row {
            display: table-row;
        }
        .invoice-info-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .info-value {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #333;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .total-row--final {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 10px 0;
            margin-top: 10px;
        }
        .address-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .address-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Order #{{ $order->order_number }}</p>
    </div>

    <div class="invoice-info">
        <div class="invoice-info-row">
            <div class="invoice-info-cell">
                <div class="info-label">Invoice Date:</div>
                <div class="info-value">{{ $order->created_at->format('F d, Y') }}</div>

                <div class="info-label" style="margin-top: 15px;">Order Number:</div>
                <div class="info-value">{{ $order->order_number }}</div>

                <div class="info-label" style="margin-top: 15px;">Payment Status:</div>
                <div class="info-value" style="text-transform: capitalize;">{{ $order->payment_status }}</div>
            </div>
            <div class="invoice-info-cell">
                <div class="address-box">
                    <div class="address-title">Bill To:</div>
                    <div>
                        {{ $order->billing_full_name }}<br>
                        {{ $order->billing_street_address }}<br>
                        @if($order->billing_suburb){{ $order->billing_suburb }}, @endif
                        {{ $order->billing_city }}<br>
                        @if($order->billingRegion){{ $order->billingRegion->name }}, @endif
                        {{ $order->billing_zip_code }}<br>
                        {{ $order->billing_country }}<br>
                        Email: {{ $order->billing_email }}<br>
                        Phone: {{ $order->billing_phone }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">${{ number_format($item->price, 2) }}</td>
                <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>${{ number_format($order->subtotal, 2) }}</span>
        </div>
        @if($order->discount > 0)
        <div class="total-row">
            <span>Discount ({{ $order->coupon_code }}):</span>
            <span>-${{ number_format($order->discount, 2) }}</span>
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

    <div style="clear: both; margin-top: 30px;">
        <div class="address-box" style="width: 48%; float: left; margin-right: 2%;">
            <div class="address-title">Shipping Address:</div>
            <div>
                {{ $order->shipping_full_name }}<br>
                {{ $order->shipping_street_address }}<br>
                @if($order->shipping_suburb){{ $order->shipping_suburb }}, @endif
                {{ $order->shipping_city }}<br>
                @if($order->shippingRegion){{ $order->shippingRegion->name }}, @endif
                {{ $order->shipping_zip_code }}<br>
                {{ $order->shipping_country }}
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is an automated invoice. Please keep this for your records.</p>
    </div>
</body>
</html>
