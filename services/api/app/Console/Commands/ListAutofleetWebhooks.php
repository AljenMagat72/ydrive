<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ListAutofleetWebhooks extends Command
{
    protected $signature = 'app:list-autofleet-webhooks';
    protected $description = 'Get a list of all active AutoFleet webhooks';

    public function handle()
    {
        $url = 'https://api.autofleet.io/api/v1/listener';
        $token = env('AUTOFLEET_JWT_TOKEN');

        if (!$token) {
            $this->error('AUTOFLEET_JWT_TOKEN not found in .env');
            return;
        }

        $this->info('Fetching active listeners from AutoFleet...');

        $response = Http::withToken($token)
            ->get($url);

        if ($response->successful()) {
            $listeners = $response->json();

            if (empty($listeners)) {
                $this->warn('No webhooks found.');
                return;
            }

            $this->table(
                ['ID', 'Action', 'Target URL', 'Created At'],
                collect($listeners)->map(fn($item) => [
                    $item['id'],
                    is_array($item['action']) ? implode(', ', $item['action']) : $item['action'],
                    $item['target'],
                    $item['createdAt']
                ])
            );
        } else {
            $this->error('Failed to fetch listeners: ' . $response->status());
            $this->line($response->body());
        }
    }
}