<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission</title>
</head>
<body style="margin: 0; padding: 0; background-color: #eef2f7; font-family: Arial, Helvetica, sans-serif; color: #1f2937;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #eef2f7; margin: 0; padding: 26px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 700px; background-color: #ffffff; border: 1px solid #dbe4ef; border-radius: 18px; overflow: hidden; box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="padding: 32px 38px; background: linear-gradient(135deg, #0b3f83 0%, #1559b8 100%); color: #ffffff;">
                            <div style="font-size: 12px; letter-spacing: 1.6px; text-transform: uppercase; opacity: 0.9; font-weight: 700;">Kotheppuram Family Website</div>
                            <h1 style="margin: 10px 0 0; font-size: 28px; line-height: 1.3; font-weight: 700;">New Contact Message</h1>
                            <p style="margin: 10px 0 0; font-size: 14px; line-height: 1.7; color: rgba(255, 255, 255, 0.92);">
                                A new inquiry has been submitted through the Contact Us form.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 28px 38px 8px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 18px; background-color: #f8fbff; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #475569; width: 140px;"><strong>Name</strong></td>
                                    <td style="padding: 16px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0f172a;">{{ $name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 18px; background-color: #f8fbff; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #475569;"><strong>Email</strong></td>
                                    <td style="padding: 16px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0f172a;">
                                        <a href="mailto:{{ $email }}" style="color: #1d4ed8; text-decoration: none;">{{ $email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 18px; background-color: #f8fbff; font-size: 13px; color: #475569;"><strong>Subject</strong></td>
                                    <td style="padding: 16px 18px; font-size: 14px; color: #0f172a;">{{ $subjectLine }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 14px 38px 30px;">
                            <div style="font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #1d4ed8; margin-bottom: 10px;">Message</div>
                            <div style="padding: 18px 20px; border: 1px solid #dbeafe; border-radius: 12px; background-color: #f8fbff; font-size: 15px; line-height: 1.85; color: #334155; white-space: pre-wrap;">{!! nl2br(e($messageBody)) !!}</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 22px 38px 28px; background-color: #0f172a; color: #cbd5e1; font-size: 14px; line-height: 1.8;">
                            Sent from Contact Us • Kotheppuram Family
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>