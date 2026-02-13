<?php

namespace Modules\Zoho\Actions;

use App\Models\Driver;
use Illuminate\Support\Facades\Http;
use Modules\Zoho\Services\ZohoService;

class SyncDriverToZohoAction
{
    public function handle($driver)
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $driver->phone_number);
        $searchSuffix = substr($cleanPhone, -10);

        if (strlen($searchSuffix) < 10) {
            dump("Skipping: {$driver->phone_number} (Too short)");
            return null;
        }

        $zoho = app(ZohoService::class);
        $accessToken = $zoho->refreshToken();

        $url = "https://www.zohoapis.com/crm/v2/search?phone=" . $searchSuffix;
        
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get($url);
        
        $results = $response->json();

        // DEBUG: See exactly what Zoho is sending back
        if ($response->failed()) {
            dump("API Error: " . $response->body());
        }

        if (isset($results['data'][0])) {
            $zohoId = $results['data'][0]['id'];
            
            // Check if the update actually happens
            $updated = $driver->update(['zoho_id' => $zohoId]);
            
            dump("FOUND! Suffix: {$searchSuffix} -> ZohoID: {$zohoId} (DB Updated: " . ($updated ? 'YES' : 'NO') . ")");
            
            return $zohoId;
        }

        // DEBUG: If no match, show what suffix failed
        dump("No match in Zoho for suffix: {$searchSuffix}");

        return null;
    }
}