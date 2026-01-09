<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Cancelled</title>
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

                    <!-- Header Banner - Red (Cancelled) -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #dc3545;">
                            <!-- X Icon -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px auto;">
                                <tr>
                                    <td style="padding: 0; vertical-align: middle;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border: 3px solid #ffffff; border-radius: 50%; background-color: transparent; width: 60px; height: 60px;">
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; padding: 0;">
                                                    <span style="color: #ffffff; font-size: 30px; font-weight: 700;">✕</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- Order cancelled text -->
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                ORDER CANCELLED
                            </p>
                            <!-- Main heading -->
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Your Order Has Been Cancelled
                            </h1>
                        </td>
                    </tr>

                    <!-- Order Summary -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {{ $order->billing_full_name }},
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We're sorry to inform you that your order <strong>#{{ $order->order_number }}</strong> has been cancelled. If you made a payment, you will receive a refund within 5-7 business days.
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
                                                    <p style="margin: 5px 0 0 0; color: #dc3545; font-size: 16px; font-weight: 700;">
                                                        Cancelled
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
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <h2 style="margin: 0 0 20px 0; color: #374E94; font-size: 20px; font-weight: 700; border-bottom: 2px solid #e9ecef; padding-bottom: 10px;">
                                Cancelled Items
                            </h2>

            @foreach($order->items as $item)
                            <!-- Product Item -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px; border-bottom: 1px solid #e9ecef; padding-bottom: 20px;">
                                <tr>
                                    <td width="100" style="padding-right: 15px; vertical-align: top;">
                                        @php
                                            $productImage = asset('assets/images/placeholder.jpg');
                                            if ($item->product && isset($item->product->main_image_url)) {
                                                $productImage = $item->product->main_image_url;
                                            } elseif ($item->product && $item->product->images && $item->product->images->count() > 0) {
                                                $productImage = $item->product->images->first()->image_url ?? asset('assets/images/placeholder.jpg');
                                            }
                                        @endphp
                                        <img src="{{ $productImage }}" alt="{{ $item->product_name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; display: block; opacity: 0.6;" />
                                    </td>
                                    <td style="vertical-align: top;">
                                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 16px; font-weight: 600;">
                                            {{ $item->product_name }}
                                        </p>
                                        @if($item->product && $item->product->eposnow_product_id)
                                        <p style="margin: 0 0 8px 0; color: #666666; font-size: 14px; font-weight: 400;">
                                            SKU: {{ $item->product->eposnow_product_id }}
                                        </p>
                                        @endif
                                        <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400;">
                                            Quantity: {{ $item->quantity }}
                                        </p>
                                    </td>
                                    <td align="right" style="vertical-align: top;">
                                        <p style="margin: 0; color: #666666; font-size: 18px; font-weight: 700; text-decoration: line-through;">
                    ${{ number_format($item->subtotal, 2) }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
            @endforeach
                        </td>
                    </tr>

                    <!-- Refund Information -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8d7da; border-left: 4px solid #dc3545; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #721c24; font-size: 16px; font-weight: 600;">
                                            💳 Refund Information
                                        </p>
                                        <p style="margin: 0; color: #721c24; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            @if($order->payment_status === 'paid')
                                            A refund of <strong>${{ number_format($order->total, 2) }}</strong> will be processed to your original payment method within 5-7 business days. You will receive a confirmation email once the refund is processed.
                                            @else
                                            No payment was processed for this order, so no refund is required.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>
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
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400; text-decoration: line-through;">
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
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400; text-decoration: line-through;">
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
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400; text-decoration: line-through;">
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
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400; text-decoration: line-through;">
                                                        ${{ number_format($order->tax, 2) }}
                                                    </p>
                                                </td>
                                            </tr>
            @endif
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #dc3545; font-size: 18px; font-weight: 700;">
                                                        Total (Cancelled)
                                                    </p>
                                                </td>
                                                <td align="right" style="padding-top: 15px; border-top: 2px solid #e9ecef;">
                                                    <p style="margin: 0; color: #dc3545; font-size: 24px; font-weight: 700; text-decoration: line-through;">
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

                    <!-- Help Section -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #d1ecf1; border-left: 4px solid #0c5460; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #0c5460; font-size: 16px; font-weight: 600;">
                                            ❓ Need Help?
                                        </p>
                                        <p style="margin: 0; color: #0c5460; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                            If you have any questions about this cancellation or would like to place a new order, please don't hesitate to contact our customer service team. We're here to help!
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- View Order Button -->
                    @if(isset($orderViewUrl))
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{{ $orderViewUrl }}" style="display: inline-block; padding: 16px 40px; background-color: #6c757d; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VIEW ORDER DETAILS
                            </a>
                        </td>
                    </tr>
                    @endif

                    <!-- Closing -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We apologize for any inconvenience this may have caused. We hope to serve you better in the future.
                            </p>
                            <p style="margin: 0 0 5px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Thanks,
                            </p>
                            <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 400;">
                                The PaperWings Team
                            </p>
                        </td>
                    </tr>

                    <!-- Footer - Contact Information -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #f5f5f5;">
                            <p style="margin: 0 0 15px 0; color: #374E94; font-size: 16px; font-weight: 600; text-align: center;">
                                Get in touch
                            </p>
                            @if(isset($contactPhone))
                            <p style="margin: 0 0 8px 0; color: #000000; font-size: 14px; font-weight: 400; text-align: center;">
                                <a href="tel:{{ $contactPhone }}" style="color: #000000; text-decoration: none;">{{ $contactPhone }}</a>
                            </p>
                            @endif
                            @if(isset($contactEmail))
                            <p style="margin: 0 0 25px 0; color: #000000; font-size: 14px; font-weight: 400; text-align: center;">
                                <a href="mailto:{{ $contactEmail }}" style="color: #000000; text-decoration: none;">{{ $contactEmail }}</a>
                            </p>
                            @endif

                            <!-- Social Media Icons -->
                            @if(isset($socialLinks) && !empty($socialLinks))
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 0;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                @if(isset($socialLinks['facebook']) && !empty($socialLinks['facebook']))
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['facebook'] }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px; font-weight: 700;">f</span>
                                                    </a>
                                                </td>
                                                @endif
                                                @if(isset($socialLinks['instagram']) && !empty($socialLinks['instagram']))
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['instagram'] }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px; font-weight: 700;">i</span>
                                                    </a>
                                                </td>
                                                @endif
                                                @if(isset($socialLinks['twitter']) && !empty($socialLinks['twitter']))
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['twitter'] }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 14px; font-weight: 700;">t</span>
                                                    </a>
                                                </td>
                                                @endif
                                                @if(isset($socialLinks['linkedin']) && !empty($socialLinks['linkedin']))
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['linkedin'] }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 12px; font-weight: 700;">in</span>
                                                    </a>
                                                </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif
                        </td>
                    </tr>

                    <!-- Copyright Bar - Dark Blue -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; background-color: #374E94;">
                            <p style="margin: 0; color: #ffffff; font-size: 12px; font-weight: 400;">
                                Copyrights © {{ date('Y') }} PaperWings All Rights Reserved
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
