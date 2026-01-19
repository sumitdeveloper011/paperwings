<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Processing</title>
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

                    <!-- Header Banner - Blue (Processing) -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #374E94;">
                            <!-- Processing Icon -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px auto;">
                                <tr>
                                    <td style="padding: 0; vertical-align: middle;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border: 3px solid #ffffff; border-radius: 50%; background-color: transparent; width: 60px; height: 60px;">
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; padding: 0;">
                                                    <span style="color: #ffffff; font-size: 30px; font-weight: 700;">⚙</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- Order processing text -->
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                ORDER PROCESSING
                            </p>
                            <!-- Main heading -->
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Your Order is Being Processed
                            </h1>
                        </td>
                    </tr>

                    <!-- Order Summary -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {{ $order->billing_first_name }} {{ $order->billing_last_name }},
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Great news! Your order <strong>#{{ $order->order_number }}</strong> has been confirmed and is now being processed. We're preparing your items for shipment.
                            </p>

                            <!-- Order Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 12px;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Number
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #374E94; font-size: 18px; font-weight: 700;">
                                                        #{{ $order->order_number }}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 12px;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Date
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #000000; font-size: 18px; font-weight: 600;">
                                                        {{ $order->created_at->format('F d, Y') }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 12px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Order Status
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #374E94; font-size: 16px; font-weight: 700;">
                                                        Processing ⚙
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We'll send you another email with tracking information once your order ships. You can expect your order to be processed within 1-2 business days.
                            </p>
                        </td>
                    </tr>

                    <!-- Order Items Summary -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <h2 style="margin: 0 0 20px 0; color: #374E94; font-size: 20px; font-weight: 700; border-bottom: 2px solid #e9ecef; padding-bottom: 10px;">
                                Order Summary
                            </h2>

            @foreach($order->items as $item)
                            <!-- Product Item -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px; border-bottom: 1px solid #e9ecef; padding-bottom: 20px;">
                                <tr>
                                    <td width="100" style="padding-right: 15px; vertical-align: top;">
                                        @php
                                            $productImage = asset('assets/images/placeholder.jpg');
                                            if ($item->product && isset($item->product->main_thumbnail_url)) {
                                                $productImage = $item->product->main_thumbnail_url;
                                            } elseif ($item->product && isset($item->product->main_image_url)) {
                                                $productImage = $item->product->main_image_url;
                                            } elseif ($item->product && $item->product->images && $item->product->images->count() > 0) {
                                                $productImage = $item->product->images->first()->thumbnail_url ?? $item->product->images->first()->image_url ?? asset('assets/images/placeholder.jpg');
                                            }
                                        @endphp
                                        <img src="{{ $productImage }}" alt="{{ $item->product_name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; display: block;" />
                                    </td>
                                    <td style="vertical-align: top;">
                                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 16px; font-weight: 600;">
                                            {{ $item->product_name }}
                                        </p>
                                        <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                            Quantity: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                        </p>
                                    </td>
                                    <td align="right" style="vertical-align: top;">
                                        <p style="margin: 0; color: #e95c67; font-size: 18px; font-weight: 700;">
                    ${{ number_format($item->subtotal, 2) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
            @endforeach
                        </td>
                    </tr>

                    <!-- Order Totals -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Subtotal
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        ${{ number_format($order->subtotal, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
            @if($order->discount > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        -${{ number_format($order->discount, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
                                            @endif
                                            @if(($order->shipping_price ?? $order->shipping) > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Shipping
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        ${{ number_format($order->shipping_price ?? $order->shipping, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
            @endif
                                            @if($order->tax > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Tax
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        ${{ number_format($order->tax, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
            @endif
                                            @if($order->platform_fee > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Platform Fee
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        ${{ number_format($order->platform_fee, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($order->stripe_fee > 0)
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                        Processing Fee
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-bottom: 10px;">
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        ${{ number_format($order->stripe_fee, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #374E94; font-size: 18px; font-weight: 700;">
                                                        Total
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #e95c67; font-size: 24px; font-weight: 700;">
                                                        ${{ number_format($order->total, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Next Steps -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #e7f3ff; border-left: 4px solid #374E94; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #374E94; font-size: 16px; font-weight: 600;">
                                            📦 What's Next?
                                        </p>
                                        <p style="margin: 0; color: #374E94; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            We're preparing your order for shipment. You'll receive a shipping confirmation email with tracking details once your order is on its way.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Closing -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Thank you for your order! We appreciate your business and look forward to serving you again.
                            </p>
                            <p style="margin: 0 0 5px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Thanks,
                            </p>
                            <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 400;">
                                The {{ config('app.name') }} Team
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
