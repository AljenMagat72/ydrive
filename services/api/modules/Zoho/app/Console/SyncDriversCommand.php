<?php

namespace Modules\Zoho\Console;

use App\Models\Driver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Modules\Zoho\Services\ZohoService;

class SyncDriversCommand extends Command
{
    protected $signature = 'zoho:sync-drivers';
    protected $description = 'Sync drivers to Zoho using Lead and Contact search';

    public function handle()
    {
        $this->info("Force-refreshing Zoho Token...");

        $response = \Illuminate\Support\Facades\Http::asForm()->post("https://accounts.zoho.com/oauth/v2/token", [
            'refresh_token' => config('zoho.refresh_token'),
            'client_id'     => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'grant_type'    => 'refresh_token',
        ]);

        if (!$response->successful()) {
            $this->error("Zoho API Error: " . $response->body());
            return;
        }

        $accessToken = $response->json()['access_token'];
        $this->info("Token acquired. Starting bulk sync...");

        // Get all drivers missing a Zoho ID
        $drivers = \App\Models\Driver::whereNull('zoho_id')
            ->whereNotNull('phone_number')
            ->get();

        $this->info("Processing " . $drivers->count() . " drivers...");

        foreach ($drivers as $driver) {
            // Clean the number (e.g., 639953706471)
            $cleanPhone = preg_replace('/[^0-9]/', '', $driver->phone_number);

            if (strlen($cleanPhone) < 10) {
                $this->warn("Skipping {$driver->phone_number}: Too short.");
                continue;
            }

            // Logic that worked: Search Contacts, then Leads, using ONLY 'Phone'
            $zohoId = $this->searchZoho($accessToken, $cleanPhone);

            if ($zohoId) {
                $driver->update(['zoho_id' => $zohoId]);
                $this->info("✓ Matched: {$cleanPhone} -> {$zohoId}");
            } else {
                $this->error("× No match found for: {$cleanPhone}");
            }

            // 200ms delay to stay within API rate limits (5 requests per second)
            usleep(200000);
        }

        $this->info("Sync complete!");
    }

    /**
     * Helper to search both Modules
     */
    private function searchZoho($token, $number)
    {
        // Check both raw and with + prefix
        $criteria = "((Phone:equals:{$number})OR(Phone:equals:+{$number}))";
        
        // 1. Try Contacts
        $url = "https://www.zohoapis.com/crm/v2/Contacts/search?criteria=" . urlencode($criteria);
        $res = \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => 'Zoho-oauthtoken ' . $token])->get($url);
        $data = $res->json();

        if (isset($data['data'][0])) {
            return $data['data'][0]['id'];
        }

        // 2. Try Leads
        $url = "https://www.zohoapis.com/crm/v2/Leads/search?criteria=" . urlencode($criteria);
        $res = \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => 'Zoho-oauthtoken ' . $token])->get($url);
        $data = $res->json();

        return $data['data'][0]['id'] ?? null;
    }
}