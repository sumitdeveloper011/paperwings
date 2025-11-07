<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Email Verification</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #ffffff; line-height: 1.6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff;">
                    <!-- Top Bar - Logo -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; background-color: #ffffff;">
                            <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto;" />
                        </td>
                    </tr>
                    
                    <!-- Header Banner - Dark Blue -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #374E94;">
                            <!-- Envelope Icon with Lines -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px auto;">
                                <tr>
                                    <td style="padding: 0 5px; vertical-align: middle;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="width: 8px; height: 2px; background-color: #ffffff;"></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding: 0; vertical-align: middle;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border: 2px solid #ffffff; border-radius: 4px; background-color: transparent;">
                                            <tr>
                                                <td style="width: 40px; height: 30px; text-align: center; vertical-align: top; padding: 0;">
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: -2px auto 0 auto;">
                                                        <tr>
                                                            <td style="width: 20px; height: 15px; border: 2px solid #ffffff; border-bottom: none; border-radius: 4px 4px 0 0; background-color: #374E94;"></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding: 0 5px; vertical-align: middle;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="width: 8px; height: 2px; background-color: #ffffff;"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- Thanks for signing up text -->
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                THANKS FOR SIGNING UP!
                            </p>
                            <!-- Main heading -->
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Verify Your E-mail Address
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {{ $userName }},
                            </p>
                            <!-- Main content -->
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                You're almost ready to get started. Please click on the button below to verify your email address and enjoy exclusive services with us!
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Verify Button - Coral Red -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                VERIFY YOUR EMAIL
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Expiry Notice -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <p style="margin: 0; color: #666666; font-size: 12px; font-weight: 400;">
                                This verification link will expire in 60 minutes.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Closing -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 5px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Thanks,
                            </p>
                            <p style="margin: 0; color: #000000; font-size: 16px; font-weight: 400;">
                                The Paper Wings Team
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer - Contact Information -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #f5f5f5;">
                            <p style="margin: 0 0 15px 0; color: #374E94; font-size: 16px; font-weight: 600; text-align: center;">
                                Get in touch
                            </p>
                            <p style="margin: 0 0 8px 0; color: #000000; font-size: 14px; font-weight: 400; text-align: center;">
                                {{ $contactPhone ?? '+880 123 4567' }}
                            </p>
                            <p style="margin: 0 0 25px 0; color: #000000; font-size: 14px; font-weight: 400; text-align: center;">
                                {{ $contactEmail ?? 'info@paperwings.com' }}
                            </p>
                            
                            <!-- Social Media Icons -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 0;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <!-- Facebook -->
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['facebook'] ?? '#' }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px; font-weight: 700;">f</span>
                                                    </a>
                                                </td>
                                                <!-- Instagram -->
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['instagram'] ?? '#' }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px; font-weight: 700;">i</span>
                                                    </a>
                                                </td>
                                                <!-- Twitter -->
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['twitter'] ?? '#' }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 14px; font-weight: 700;">t</span>
                                                    </a>
                                                </td>
                                                <!-- LinkedIn -->
                                                <td style="padding: 0 8px;">
                                                    <a href="{{ $socialLinks['linkedin'] ?? '#' }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 12px; font-weight: 700;">in</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Copyright Bar - Dark Blue -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; background-color: #374E94;">
                            <p style="margin: 0; color: #ffffff; font-size: 12px; font-weight: 400;">
                                Copyrights © {{ date('Y') }} Paper Wings All Rights Reserved
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

