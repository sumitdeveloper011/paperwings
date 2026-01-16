<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Newsletter</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f5f5f5; line-height: 1.6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; background-color: #ffffff; border-bottom: 1px solid #e0e0e0;">
                            @if(isset($logoUrl))
                            <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto;" />
                            @endif
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <div style="color: #333333; font-size: 16px; line-height: 1.8;">
                                {!! $body !!}
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f8f9fa; border-top: 1px solid #e0e0e0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 20px;">
                                        @if(isset($socialLinks) && count($socialLinks) > 0)
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                                            <tr>
                                                @if(isset($socialLinks['facebook']))
                                                <td style="padding: 0 10px;">
                                                    <a href="{{ $socialLinks['facebook'] }}" style="display: inline-block; width: 32px; height: 32px; background-color: #1877f2; border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px;">f</span>
                                                    </a>
                                                </td>
                                                @endif
                                                @if(isset($socialLinks['instagram']))
                                                <td style="padding: 0 10px;">
                                                    <a href="{{ $socialLinks['instagram'] }}" style="display: inline-block; width: 32px; height: 32px; background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px;">📷</span>
                                                    </a>
                                                </td>
                                                @endif
                                                @if(isset($socialLinks['twitter']))
                                                <td style="padding: 0 10px;">
                                                    <a href="{{ $socialLinks['twitter'] }}" style="display: inline-block; width: 32px; height: 32px; background-color: #1da1f2; border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px;">🐦</span>
                                                    </a>
                                                </td>
                                                @endif
                                                @if(isset($socialLinks['linkedin']))
                                                <td style="padding: 0 10px;">
                                                    <a href="{{ $socialLinks['linkedin'] }}" style="display: inline-block; width: 32px; height: 32px; background-color: #0077b5; border-radius: 50%; text-align: center; line-height: 32px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px;">in</span>
                                                    </a>
                                                </td>
                                                @endif
                                            </tr>
                                        </table>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center; padding-bottom: 15px;">
                                        <p style="margin: 0; color: #666666; font-size: 14px;">
                                            @if(isset($contactEmail))
                                            Contact us: <a href="mailto:{{ $contactEmail }}" style="color: #374E94; text-decoration: none;">{{ $contactEmail }}</a>
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                                @if(isset($unsubscribeUrl))
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0; color: #999999; font-size: 12px;">
                                            <a href="{{ $unsubscribeUrl }}" style="color: #999999; text-decoration: underline;">Unsubscribe from this newsletter</a>
                                        </p>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="text-align: center; padding-top: 15px;">
                                        <p style="margin: 0; color: #999999; font-size: 12px;">
                                            © {{ date('Y') }} Paper Wings. All rights reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
