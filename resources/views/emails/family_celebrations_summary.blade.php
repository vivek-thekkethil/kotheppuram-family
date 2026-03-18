<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotheppuram Family Celebration Summary</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fb; font-family: Arial, Helvetica, sans-serif; color: #1f2937;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fb; margin: 0; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 680px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 12px 36px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="padding: 36px 40px 28px; background: linear-gradient(135deg, #0f766e 0%, #2563eb 100%); color: #ffffff;">
                            <div style="font-size: 13px; letter-spacing: 1.6px; text-transform: uppercase; opacity: 0.88;">Kotheppuram Family</div>
                            <h1 style="margin: 12px 0 10px; font-size: 30px; line-height: 1.2; font-weight: 700;">Daily Celebration Summary</h1>
                            <p style="margin: 0; font-size: 16px; line-height: 1.7; color: rgba(255, 255, 255, 0.92);">
                                Summary for {{ $today->format('d M Y') }}.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 28px 16px;">
                            @foreach ($todayAlerts as $alert)
                                @php
                                    $isBirthday = $alert['type'] === 'birthday';
                                @endphp

                                @if ($isBirthday)
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 14px; border: 1px solid #e2e8f0; border-radius: 16px; background-color: #fff7ed;">
                                        <tr>
                                            <td width="6" style="background-color: #f59e0b;"></td>
                                            <td style="padding: 18px 22px;">
                                                <div style="font-size: 13px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: #f59e0b; margin-bottom: 8px;">
                                                    Birthday
                                                </div>
                                                <div style="font-size: 20px; font-weight: 700; line-height: 1.4; color: #0f172a; margin-bottom: 6px;">
                                                    {{ $alert['display_name'] ?? $alert['member']->name }}
                                                </div>
                                                <p style="margin: 0; font-size: 14px; line-height: 1.8; color: #475569;">
                                                    Please reach out with your wishes and blessings today.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 14px; border: 1px solid #e2e8f0; border-radius: 16px; background-color: #fdf2f8;">
                                        <tr>
                                            <td width="6" style="background-color: #ec4899;"></td>
                                            <td style="padding: 18px 22px;">
                                                <div style="font-size: 13px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: #ec4899; margin-bottom: 8px;">
                                                    Wedding Anniversary
                                                </div>
                                                <div style="font-size: 20px; font-weight: 700; line-height: 1.4; color: #0f172a; margin-bottom: 6px;">
                                                    {{ $alert['display_name'] ?? $alert['member']->name }}
                                                </div>
                                                <p style="margin: 0; font-size: 14px; line-height: 1.8; color: #475569;">
                                                    Please reach out with your wishes and blessings today.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                            @endforeach
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 8px 40px 32px;">
                            <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #475569;">
                                Thank you for helping us celebrate these important family moments with care and warmth.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 40px 32px; background-color: #0f172a; color: #cbd5e1;">
                            <div style="font-size: 15px; line-height: 1.8;">Warm regards,<br><strong style="color: #ffffff;">Kotheppuram Family</strong></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>