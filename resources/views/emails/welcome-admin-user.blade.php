<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to Admin Panel</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #ffffff; line-height: 1.6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff;">
                    @include('emails.partials.header')
                    
                    <!-- Header Banner - Dark Blue -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #374E94;">
                            <!-- Shield Icon -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px auto;">
                                <tr>
                                    <td style="padding: 0; text-align: center;">
                                        <div style="width: 60px; height: 60px; background-color: #ffffff; border-radius: 50%; display: inline-block; line-height: 60px; font-size: 30px;">
                                            🛡️
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <!-- Main heading -->
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Welcome to Admin Panel
                            </h1>
                            <p style="margin: 15px 0 0 0; color: #ffffff; font-size: 16px; font-weight: 400;">
                                Your account has been created successfully
                            </p>
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
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Your admin account has been created for the Paper Wings admin panel. You can now access the admin dashboard with the following credentials:
                            </p>
                            
                            <!-- Login Details Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 15px 0; color: #000000; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Login Details
                                        </p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px; font-weight: 400; width: 120px;">
                                                    <strong>Email:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #000000; font-size: 14px; font-weight: 600;">
                                                    {{ $email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                    <strong>Password:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #000000; font-size: 14px; font-weight: 600; font-family: monospace;">
                                                    {{ $password }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666666; font-size: 14px; font-weight: 400;">
                                                    <strong>Role:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #000000; font-size: 14px; font-weight: 600;">
                                                    {{ $roles }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 20px 0 0 0; color: #666666; font-size: 14px; font-weight: 400; line-height: 1.6;">
                                <strong style="color: #e95c67;">⚠️ Security Notice:</strong> For security reasons, please change your password immediately after your first login.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Login Button - Coral Red -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{{ $loginUrl }}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                LOGIN TO ADMIN PANEL
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Admin Panel URL -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <p style="margin: 0 0 10px 0; color: #666666; font-size: 12px; font-weight: 400;">
                                Or copy and paste this URL into your browser:
                            </p>
                            <p style="margin: 0; color: #374E94; font-size: 12px; font-weight: 400; word-break: break-all;">
                                {{ $loginUrl }}
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
                    
                    @include('emails.partials.footer')
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
