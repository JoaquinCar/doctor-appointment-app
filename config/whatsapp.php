<?php

return [
    'api_url'         => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v21.0'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'access_token'    => env('WHATSAPP_ACCESS_TOKEN'),
    'templates' => [
        'confirmation' => env('WHATSAPP_CONFIRMATION_TEMPLATE', 'appointment_confirmation'),
        'reminder'     => env('WHATSAPP_REMINDER_TEMPLATE', 'appointment_reminder'),
    ],
    'language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'es_MX'),
];
