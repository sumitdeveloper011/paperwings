<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Password</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #ffffff; line-height: 1.6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff;">
                    <!-- Top Bar - Logo -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; background-color: #ffffff;">
                            <img src="{{$logoUrl}}" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto;" />
                        </td>
                    </tr>
                    
                    <!-- Header Banner - Dark Blue -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #374E94;">
                            <!-- Lock Icon with Lines -->
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
                            <!-- Password reset request text -->
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                PASSWORD RESET REQUEST
                            </p>
                            <!-- Main heading -->
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Reset Your Password
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {{ $userName ?? 'there' }},
                            </p>
                            <!-- Main content -->
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We received a request to reset your password. Click the button below to create a new password. If you didn't request this, please ignore this email.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Reset Password Button - Coral Red -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{{$reset_password_link}}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                RESET PASSWORD
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Alternative Link -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 10px 0; color: #999999; font-size: 14px; text-align: center;">
                                Or copy and paste this link into your browser:
                            </p>
                            <p style="margin: 0; color: #374E94; font-size: 12px; text-align: center; word-break: break-all;">
                                <a href="{{$reset_password_link}}" style="color: #374E94; text-decoration: none;">{{$reset_password_link}}</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Security Notice -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 10px 0; color: #666666; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                <strong>Security Notice:</strong> This password reset link will expire in 1 hour for your security. If you didn't request a password reset, please contact us immediately.
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
                                The Company Team
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
                                {{ $contactPhone ?? '+11 111 333 4444' }}
                            </p>
                            <p style="margin: 0 0 25px 0; color: #000000; font-size: 14px; font-weight: 400; text-align: center;">
                                {{ $contactEmail ?? 'Info@YourCompany.com' }}
                            </p>
                            
                            <!-- Social Media Icons -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 0;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <!-- Facebook -->
                                                <td style="padding: 0 8px;">
                                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px; font-weight: 700;">f</span>
                                                    </a>
                                                </td>
                                                <!-- Instagram -->
                                                <td style="padding: 0 8px;">
                                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 16px; font-weight: 700;">i</span>
                                                    </a>
                                                </td>
                                                <!-- Twitter -->
                                                <td style="padding: 0 8px;">
                                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                                        <span style="color: #ffffff; font-size: 14px; font-weight: 700;">t</span>
                                                    </a>
                                                </td>
                                                <!-- LinkedIn -->
                                                <td style="padding: 0 8px;">
                                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
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
                                Copyrights © Company All Rights Reserved
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
