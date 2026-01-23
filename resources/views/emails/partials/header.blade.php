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
                <!-- Header Banner - Dark Blue -->
                <tr>
                    <td style="padding: 50px 40px; text-align: center; background-color: #2850a3;">
                        <img src="{{ $logoUrl ?? url('assets/frontend/images/logo.png') }}" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto 30px auto;" />
                        @if(isset($headerSubtitle) && !empty($headerSubtitle))
                        <!-- Subtitle text -->
                        <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                            {{ $headerSubtitle }}
                        </p>
                        @endif
                        @if(isset($headerTitle) && !empty($headerTitle))
                        <!-- Main heading -->
                        <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                            {{ $headerTitle }}
                        </h1>
                        @endif
                    </td>
                </tr>

