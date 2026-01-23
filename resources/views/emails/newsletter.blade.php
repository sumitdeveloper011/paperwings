@include('emails.partials.header', [
    'logoUrl' => $logoUrl ?? url('assets/frontend/images/logo.png'),
    'headerSubtitle' => $headerSubtitle ?? NEWSLETTER,
    'headerTitle' => $headerTitle ?? Newsletter,
])
              

<tr>
    <td style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
        {!! $body !!}
    </td>
</tr>
<tr>
    <td style="padding: 0 40px 40px 40px; background-color: #ffffff;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px;">
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <p style="margin: 0 0 10px 0; color: #666666; font-size: 12px; font-weight: 400; line-height: 1.5;">
                        You're receiving this email because you subscribed to our newsletter.
                    </p>
                    <p style="margin: 0; color: #666666; font-size: 12px; font-weight: 400;">
                        <a href="{{ $unsubscribeUrl }}" style="color: #2850a3; text-decoration: underline;">Unsubscribe</a>
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
@include('emails.partials.footer', [
    'contactPhone' => $contactPhone,
    'contactEmail' => $contactEmail,
    'socialLinks' => $socialLinks,
    'currentYear' => date('Y'),
    'appName' => config('app.name'),
])
