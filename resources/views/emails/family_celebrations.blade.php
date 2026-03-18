<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotheppuram Family Celebration Reminder</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fb; font-family: Arial, Helvetica, sans-serif; color: #1f2937;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fb; margin: 0; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 680px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 12px 36px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="padding: 0; background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 36px 40px 28px; color: #ffffff;">
                                        <div style="font-size: 13px; letter-spacing: 1.6px; text-transform: uppercase; opacity: 0.88;">Kotheppuram Family</div>
                                        <h1 style="margin: 12px 0 10px; font-size: 32px; line-height: 1.2; font-weight: 700;">Today's Family Celebrations</h1>
                                        <p style="margin: 0; font-size: 16px; line-height: 1.7; max-width: 520px; color: rgba(255, 255, 255, 0.92);">
                                            A warm reminder to celebrate the special moments in our family today.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px 40px 12px;">
                            <p style="margin: 0 0 8px; font-size: 16px; line-height: 1.7;">Dear {{ $recipient->name }},</p>
                            <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #475569;">
                                Here is your celebration update for <strong>{{ $today->format('d M Y') }}</strong>. Please take a moment to share your wishes and make the day even more memorable.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 8px 28px 12px;">
                            @foreach ($todayAlerts as $alert)
                                @php
                                    $recipientIds = collect($alert['recipient_ids'] ?? [(int) $alert['member']->id])
                                        ->map(fn ($id) => (int) $id)
                                        ->all();
                                    $isPersonal = in_array((int) $recipient->id, $recipientIds, true);
                                    $isBirthday = $alert['type'] === 'birthday';
                                    $icon = $isBirthday ? '🎂' : '💍';
                                    $label = $isBirthday ? 'Birthday' : 'Wedding Anniversary';
                                    $headline = $isPersonal
                                        ? ($isBirthday ? 'Wishing you a very Happy Birthday!' : 'Wishing you a wonderful Happy Anniversary!')
                                        : (($alert['display_name'] ?? $alert['member']->name).' • '.$label);
                                @endphp

                                @if ($isBirthday)
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 16px; border: 1px solid #e2e8f0; border-radius: 18px; background-color: #fff7ed;">
                                        <tr>
                                            <td width="6" style="background-color: #f59e0b;"></td>
                                            <td style="padding: 22px 24px;">
                                                <div style="font-size: 13px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: #f59e0b; margin-bottom: 10px;">
                                                    {{ $icon }} {{ $label }}
                                                </div>
                                                <div style="font-size: 22px; line-height: 1.35; font-weight: 700; color: #0f172a; margin-bottom: 10px;">
                                                    {{ $headline }}
                                                </div>

                                                @if ($isPersonal)
                                                    <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #475569;">
                                                        May this special day bring you happiness, good health, and beautiful moments with your loved ones.
                                                    </p>
                                                @else
                                                    <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #475569;">
                                                        {{ $alert['display_name'] ?? $alert['member']->name }} is celebrating a birthday today. Please send your warm wishes and blessings.
                                                    </p>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 0 16px; border: 1px solid #e2e8f0; border-radius: 18px; background-color: #fdf2f8;">
                                        <tr>
                                            <td width="6" style="background-color: #ec4899;"></td>
                                            <td style="padding: 22px 24px;">
                                                <div style="font-size: 13px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: #ec4899; margin-bottom: 10px;">
                                                    {{ $icon }} {{ $label }}
                                                </div>
                                                <div style="font-size: 22px; line-height: 1.35; font-weight: 700; color: #0f172a; margin-bottom: 10px;">
                                                    {{ $headline }}
                                                </div>

                                                @if ($isPersonal)
                                                    <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #475569;">
                                                        May this special day bring you happiness, good health, and beautiful moments with your loved ones.
                                                    </p>
                                                @elseif (count($recipientIds) > 1)
                                                    <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #475569;">
                                                        {{ $alert['display_name'] ?? $alert['member']->name }} are celebrating their wedding anniversary today. A thoughtful message from you would make the occasion even more special.
                                                    </p>
                                                @else
                                                    <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #475569;">
                                                        {{ $alert['display_name'] ?? $alert['member']->name }} is celebrating a wedding anniversary today. Please share your wishes and blessings.
                                                    </p>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                            @endforeach
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 4px 40px 32px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <div style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Share the joy</div>
                                        <p style="margin: 0; font-size: 14px; line-height: 1.8; color: #475569;">
                                            A simple call, message, or blessing can make a meaningful difference. Thank you for helping keep our family connected.
                                        </p>
                                    </td>
                                </tr>
                            </table>
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