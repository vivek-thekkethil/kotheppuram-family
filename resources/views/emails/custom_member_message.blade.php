<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #eef2f7; font-family: Arial, Helvetica, sans-serif; color: #1f2937;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #eef2f7; margin: 0; padding: 28px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 700px; background-color: #ffffff; border: 1px solid #dbe4ef; border-radius: 18px; overflow: hidden; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="padding: 34px 42px; background: linear-gradient(135deg, #0b3f83 0%, #1559b8 100%); color: #ffffff;">
                            <div style="font-size: 12px; letter-spacing: 1.6px; text-transform: uppercase; opacity: 0.9; font-weight: 700;">Kotheppuram Family</div>
                            <h1 style="margin: 10px 0 0; font-size: 30px; line-height: 1.25; font-weight: 700;">{{ $subjectLine }}</h1>
                            <p style="margin: 12px 0 0; font-size: 14px; line-height: 1.7; color: rgba(255, 255, 255, 0.92);">
                                Official family communication
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px 42px 14px;">
                            <p style="margin: 0 0 14px; font-size: 16px; line-height: 1.7; color: #0f172a;">Dear {{ $member->name }},</p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 14px; background-color: #f8fbff;">
                                <tr>
                                    <td style="padding: 22px 24px; border-left: 4px solid #1d4ed8;">
                                        <div style="font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #1d4ed8; margin-bottom: 10px;">
                                            Message
                                        </div>
                                        <div style="font-size: 15px; line-height: 1.9; color: #334155; white-space: pre-wrap;">{!! nl2br(e($description)) !!}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if (!empty($attachmentName))
                        <tr>
                            <td style="padding: 2px 42px 24px;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #d7e3f5; border-radius: 12px; background-color: #f2f7ff;">
                                    <tr>
                                        <td style="padding: 14px 16px; font-size: 14px; line-height: 1.6; color: #334155;">
                                            <strong style="display: inline-block; margin-right: 4px; color: #1e40af;">Attachment:</strong>
                                            {{ $attachmentName }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding: 4px 42px 30px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;">
                                <tr>
                                    <td style="padding: 14px 16px; font-size: 13px; line-height: 1.7; color: #64748b;">
                                        This is an official custom message from Kotheppuram Family Admin.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 42px 30px; background-color: #0f172a; color: #cbd5e1;">
                            <div style="font-size: 15px; line-height: 1.85;">
                                Warm regards,<br>
                                <strong style="color: #ffffff;">Kotheppuram Family</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>