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
                            @if(isset($socialLinks['facebook']) && !empty($socialLinks['facebook']))
                            <td style="padding: 0 8px;">
                                <a href="{{ $socialLinks['facebook'] }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                    <span style="color: #ffffff; font-size: 16px; font-weight: 700;">f</span>
                                </a>
                            </td>
                            @endif
                            <!-- Instagram -->
                            @if(isset($socialLinks['instagram']) && !empty($socialLinks['instagram']))
                            <td style="padding: 0 8px;">
                                <a href="{{ $socialLinks['instagram'] }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                    <span style="color: #ffffff; font-size: 16px; font-weight: 700;">i</span>
                                </a>
                            </td>
                            @endif
                            <!-- Twitter -->
                            @if(isset($socialLinks['twitter']) && !empty($socialLinks['twitter']))
                            <td style="padding: 0 8px;">
                                <a href="{{ $socialLinks['twitter'] }}" style="display: inline-block; width: 40px; height: 40px; background-color: #000000; border-radius: 50%; text-align: center; line-height: 40px; text-decoration: none;">
                                    <span style="color: #ffffff; font-size: 14px; font-weight: 700;">t</span>
                                </a>
                            </td>
                            @endif
                            <!-- LinkedIn -->
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

