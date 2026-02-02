<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegisterAutofleetWebhook extends Command
{
    protected $signature = 'app:register-autofleet-webhook';

    protected $description = 'Registers the driver-created webhook with AutoFleet';

    public function handle()
    {
        $url = 'https://api.autofleet.io/api/v1/listener';
        $token = env('AUTOFLEET_JWT_TOKEN');
        
        $payload = [
            'action'   => ['driver-created'],
            'entityId' => env('AUTOFLEET_ENTITY_ID'),
            'target'   => env('AUTOFLEET_WEBHOOK_TARGET'),
            'type'     => 'async_webhook',
        ];

        $this->info('Sending request to AutoFleet...');

        $response = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->post($url, $payload);

        if ($response->successful()) {
            $this->info('Success!');
            $this->line(json_encode($response->json(), JSON_PRETTY_PRINT));
        } else {
            $this->error('Failed: ' . $response->status());
            $this->line($response->body());
        }
    }
}