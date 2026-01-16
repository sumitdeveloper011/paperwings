<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>New Order Received</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #ffffff; line-height: 1.6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff;">
                    <!-- Top Bar - Logo -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; background-color: #ffffff;">
                            @if(isset($logoUrl))
                            <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto;" />
                            @endif
                        </td>
                    </tr>

                    <!-- Header Banner - Blue -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #374E94;">
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                NEW ORDER RECEIVED
                            </p>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Order #{{ $order->order_number }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Order Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                A new order has been received and requires your attention:
                            </p>

                            <!-- Order Details Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Number
                                                    </p>
                                                    <p style="margin: 0; color: #374E94; font-size: 18px; font-weight: 700;">
                                                        #{{ $order->order_number }}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Total
                                                    </p>
                                                    <p style="margin: 0; color: #e95c67; font-size: 24px; font-weight: 700;">
                                                        ${{ number_format($order->total, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Customer
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 600;">
                                                        {{ $customerName }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Customer Email
                                                    </p>
                                                    <p style="margin: 0; color: #374E94; font-size: 16px; font-weight: 600;">
                                                        <a href="mailto:{{ $order->billing_email }}" style="color: #374E94; text-decoration: none;">{{ $order->billing_email }}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Status
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 600;">
                                                        {{ ucfirst($order->status) }}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Payment Status
                                                    </p>
                                                    <p style="margin: 0; color: {{ $order->payment_status === 'paid' ? '#28a745' : '#ff9800' }}; font-size: 16px; font-weight: 600;">
                                                        {{ ucfirst($order->payment_status) }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Date
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        {{ $order->created_at->format('F d, Y H:i') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Order Items Summary -->
                            <h2 style="margin: 0 0 15px 0; color: #374E94; font-size: 18px; font-weight: 700;">
                                Order Items ({{ $order->items->count() }})
                            </h2>
                            @foreach($order->items as $item)
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 5px 0; color: #000000; font-size: 15px; font-weight: 600;">
                                            {{ $item->product_name }}
                                        </p>
                                        <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                            Quantity: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p style="margin: 0; color: #e95c67; font-size: 16px; font-weight: 700;">
                                            ${{ number_format($item->subtotal, 2) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                        </td>
                    </tr>

                    <!-- View Order Button -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{{ $adminViewUrl }}" style="display: inline-block; padding: 16px 40px; background-color: #374E94; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VIEW ORDER DETAILS
                            </a>
                        </td>
                    </tr>

                    <!-- Closing -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
                            <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                Please review and process this order as soon as possible.
                            </p>
                        </td>
                    </tr>

                    @include('emails.partials.footer')
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
