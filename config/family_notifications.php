<?php

return [
    'email_recipients' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('FAMILY_NOTIFICATION_EMAILS', ''))
    ))),

    'upcoming_days' => (int) env('FAMILY_NOTIFICATION_UPCOMING_DAYS', 7),

    'run_at' => (string) env('FAMILY_NOTIFICATION_RUN_AT', '08:00'),

    'whatsapp_enabled' => filter_var(env('FAMILY_WHATSAPP_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'whatsapp_default_country_code' => (string) env('FAMILY_WHATSAPP_DEFAULT_COUNTRY_CODE', ''),

    'twilio_account_sid' => (string) env('TWILIO_ACCOUNT_SID', ''),
    'twilio_auth_token'  => (string) env('TWILIO_AUTH_TOKEN', ''),
    'twilio_whatsapp_from' => (string) env('TWILIO_WHATSAPP_FROM', ''),
];
