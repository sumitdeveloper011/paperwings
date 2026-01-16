<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome</title>
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
                            <!-- Welcome Icon -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px auto;">
                                <tr>
                                    <td style="padding: 0; text-align: center;">
                                        <div style="width: 60px; height: 60px; background-color: #ffffff; border-radius: 50%; display: inline-block; line-height: 60px; font-size: 30px;">
                                            🎉
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <!-- Main heading -->
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Welcome to {{ config('app.name') }}!
                            </h1>
                            <p style="margin: 15px 0 0 0; color: #ffffff; font-size: 16px; font-weight: 400;">
                                We're excited to have you with us
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {{ $user->name }},
                            </p>
                            <!-- Main content -->
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Thank you for joining {{ config('app.name') }}! We're thrilled to have you as part of our community.
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Your account has been successfully created. You can now explore our products, create wishlists, and start shopping!
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Get Started Button -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center; background-color: #ffffff;">
                            <a href="{{ route('home') }}" style="display: inline-block; padding: 16px 40px; background-color: #e95c67; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                START SHOPPING
                            </a>
                        </td>
                    </tr>

                    <!-- Features Section -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 0 0 20px 0; text-align: center;">
                                        <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            What You Can Do
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 15px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                                                    <p style="margin: 0 0 5px 0; color: #374E94; font-size: 16px; font-weight: 700;">
                                                        🛍️ Browse Products
                                                    </p>
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400; line-height: 1.5;">
                                                        Explore our wide range of products and find what you're looking for.
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px; background-color: #f8f9fa; border-radius: 8px; margin-top: 10px;">
                                                    <p style="margin: 0 0 5px 0; color: #374E94; font-size: 16px; font-weight: 700;">
                                                        ❤️ Create Wishlists
                                                    </p>
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400; line-height: 1.5;">
                                                        Save your favorite items for later and never miss a deal.
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 15px; background-color: #f8f9fa; border-radius: 8px; margin-top: 10px;">
                                                    <p style="margin: 0 0 5px 0; color: #374E94; font-size: 16px; font-weight: 700;">
                                                        📦 Track Orders
                                                    </p>
                                                    <p style="margin: 0; color: #666666; font-size: 14px; font-weight: 400; line-height: 1.5;">
                                                        Monitor your orders and get updates on delivery status.
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
                        <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 10px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                Need help? We're here for you!
                            </p>
                            <p style="margin: 0 0 5px 0; color: #374E94; font-size: 16px; font-weight: 600;">
                                Phone: {{ $contactPhone }}
                            </p>
                            <p style="margin: 0 0 30px 0; color: #374E94; font-size: 16px; font-weight: 600;">
                                Email: {{ $contactEmail }}
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
