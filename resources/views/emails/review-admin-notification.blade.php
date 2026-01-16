<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>New Product Review</title>
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

                    <!-- Header Banner - Orange/Warning -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #ff9800;">
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                NEW PRODUCT REVIEW
                            </p>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Review Pending Approval
                            </h1>
                        </td>
                    </tr>

                    <!-- Message Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                A new product review has been submitted and is pending your approval:
                            </p>

                            <!-- Review Details Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Product
                                                    </p>
                                                    <p style="margin: 0; color: #374E94; font-size: 18px; font-weight: 700;">
                                                        {{ $product->name }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 15px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Reviewer
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 600;">
                                                        {{ $review->name }}
                                                    </p>
                                                    <p style="margin: 5px 0 0 0; color: #374E94; font-size: 14px; font-weight: 400;">
                                                        <a href="mailto:{{ $review->email }}" style="color: #374E94; text-decoration: none;">{{ $review->email }}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 15px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Rating
                                                    </p>
                                                    <p style="margin: 0; color: #ff9800; font-size: 18px; font-weight: 700;">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rating)
                                                                ★
                                                            @else
                                                                ☆
                                                            @endif
                                                        @endfor
                                                        ({{ $review->rating }}/5)
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 15px; border-top: 1px solid #e9ecef; padding-top: 15px;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Review
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400; line-height: 1.6; white-space: pre-wrap;">
                                                        {{ $review->review }}
                                                    </p>
                                                </td>
                                            </tr>
                                            @if($review->verified_purchase)
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0; color: #28a745; font-size: 14px; font-weight: 600;">
                                                        ✓ Verified Purchase
                                                    </p>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding-top: 15px; border-top: 1px solid #e9ecef;">
                                                    <p style="margin: 0 0 5px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Submitted On
                                                    </p>
                                                    <p style="margin: 0; color: #000000; font-size: 14px; font-weight: 400;">
                                                        {{ $review->created_at->format('F d, Y \a\t g:i A') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- View Review Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ $adminViewUrl }}" style="display: inline-block; padding: 14px 30px; background-color: #374E94; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: 600; text-align: center;">
                                            View Reviews in Admin Panel
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @include('emails.partials.footer')
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
