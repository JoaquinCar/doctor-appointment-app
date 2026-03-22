<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp template message.
     *
     * @param  string  $to         Recipient phone number (raw, any format)
     * @param  string  $template   Template name registered in Meta Business Manager
     * @param  array   $parameters Ordered list of parameter values for the template body
     * @return bool    True on success, false on any failure (never throws)
     */
    public function sendTemplate(string $to, string $template, array $parameters): bool
    {
        $phoneNumberId = config('whatsapp.phone_number_id');
        $accessToken   = config('whatsapp.access_token');

        if (empty($phoneNumberId) || empty($accessToken)) {
            Log::warning('WhatsApp credentials not configured — message skipped', [
                'template' => $template,
                'to'       => $to,
            ]);
            return false;
        }

        $normalizedPhone = $this->normalizePhone($to);

        if (empty($normalizedPhone)) {
            Log::warning('WhatsApp: invalid or empty phone number', ['raw' => $to]);
            return false;
        }

        $url = rtrim(config('whatsapp.api_url'), '/') . "/{$phoneNumberId}/messages";

        $components = [];
        if (!empty($parameters)) {
            $components[] = [
                'type'       => 'body',
                'parameters' => array_map(
                    fn(string $value) => ['type' => 'text', 'text' => $value],
                    $parameters
                ),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $normalizedPhone,
            'type'              => 'template',
            'template'          => [
                'name'     => $template,
                'language' => ['code' => config('whatsapp.language', 'es_MX')],
                'components' => $components,
            ],
        ];

        try {
            $http = Http::withToken($accessToken)->timeout(10);

            // On local/Windows, PHP often lacks the CA bundle — skip SSL verification
            if (app()->environment('local')) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp API error', [
                'template' => $template,
                'to'       => $normalizedPhone,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp request exception', [
                'template' => $template,
                'to'       => $normalizedPhone,
                'error'    => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Normalize a phone number to E.164 format (Mexican numbers: prepend 52).
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return '';
        }

        // Already includes country code (11+ digits starting with 52 or other code)
        if (strlen($digits) >= 11) {
            return $digits;
        }

        // 10-digit local Mexican number → prepend 52
        if (strlen($digits) === 10) {
            return '52' . $digits;
        }

        return $digits;
    }
}
