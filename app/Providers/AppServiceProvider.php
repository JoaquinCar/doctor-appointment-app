<?php

namespace App\Providers;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Resend\Client as ResendClient;
use Resend\Contracts\Client as ResendClientContract;
use Resend\Transporters\HttpTransporter;
use Resend\ValueObjects\ApiKey;
use Resend\ValueObjects\Transporter\BaseUri;
use Resend\ValueObjects\Transporter\Headers;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // En Windows, Guzzle no encuentra el bundle de CA para cURL.
        // Sobreescribimos el binding del cliente de Resend para inyectar
        // un GuzzleClient configurado con el bundle de CA de Composer.
        if ($this->app->environment('local')) {
            $caBundle = \Composer\CaBundle\CaBundle::getBundledCaBundlePath();

            $this->app->singleton(ResendClientContract::class, function () use ($caBundle) {
                $apiKey  = ApiKey::from(config('resend.api_key') ?? config('services.resend.key'));
                $baseUri = BaseUri::from(getenv('RESEND_BASE_URL') ?: 'api.resend.com');
                $headers = Headers::withAuthorization($apiKey);

                $guzzle      = new GuzzleClient(['verify' => $caBundle]);
                $transporter = new HttpTransporter($guzzle, $baseUri, $headers);

                return new ResendClient($transporter);
            });

            $this->app->alias(ResendClientContract::class, 'resend');
            $this->app->alias(ResendClientContract::class, ResendClient::class);
        }
    }

    public function boot(): void
    {
        // En local, Resend sandbox solo permite enviar al correo del dueño de la cuenta
        if ($this->app->environment('local')) {
            $adminEmail = env('MAIL_ADMIN_ADDRESS');
            if ($adminEmail) {
                Mail::alwaysTo($adminEmail);
            }
        }
    }
}
