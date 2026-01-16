<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Contact Message Status Update</title>
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

                    <!-- Header Banner - Dark Blue -->
                    <tr>
                        <td style="padding: 50px 40px; text-align: center; background-color: #374E94;">
                            <p style="margin: 0 0 15px 0; color: #ffffff; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                STATUS UPDATE
                            </p>
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; line-height: 1.2;">
                                Your Message Status Has Been Updated
                            </h1>
                        </td>
                    </tr>

                    <!-- Message Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; background-color: #ffffff;">
                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400;">
                                Hi {{ $contactMessage->name }},
                            </p>
                            <p style="margin: 0 0 30px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                We wanted to inform you that the status of your contact message has been updated.
                            </p>

                            <!-- Message Details Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px 0; color: #666666; font-size: 13px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Subject
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: #374E94; font-size: 18px; font-weight: 700;">
                                            {{ $contactMessage->subject }}
                                        </p>
                                        
                                        <!-- Status Update -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 20px;">
                                            <tr>
                                                <td style="padding: 15px; background-color: #ffffff; border-radius: 6px; border-left: 4px solid #374E94;">
                                                    <p style="margin: 0 0 8px 0; color: #666666; font-size: 12px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Previous Status
                                                    </p>
                                                    <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; font-weight: 600;">
                                                        {{ $oldStatus }}
                                                    </p>
                                                    <p style="margin: 0 0 8px 0; color: #666666; font-size: 12px; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        New Status
                                                    </p>
                                                    <p style="margin: 0; color: #28a745; font-size: 18px; font-weight: 700;">
                                                        {{ $newStatus }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        @if(isset($newStatusValue) && $newStatusValue === 'solved')
                                        <p style="margin: 0 0 15px 0; color: #28a745; font-size: 14px; font-weight: 600; text-align: center; padding: 12px; background-color: #d4edda; border-radius: 6px;">
                                            ✓ Your inquiry has been resolved!
                                        </p>
                                        @elseif(isset($newStatusValue) && $newStatusValue === 'in_progress')
                                        <p style="margin: 0 0 15px 0; color: #17a2b8; font-size: 14px; font-weight: 600; text-align: center; padding: 12px; background-color: #d1ecf1; border-radius: 6px;">
                                            ⏳ We're working on your request
                                        </p>
                                        @elseif(isset($newStatusValue) && $newStatusValue === 'closed')
                                        <p style="margin: 0 0 15px 0; color: #6c757d; font-size: 14px; font-weight: 600; text-align: center; padding: 12px; background-color: #e2e3e5; border-radius: 6px;">
                                            ✓ This case has been closed
                                        </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 20px 0; color: #000000; font-size: 16px; font-weight: 400; line-height: 1.6;">
                                If you have any questions or need further assistance, please feel free to contact us:
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
