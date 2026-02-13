<?php

namespace Modules\Zoho\Services;

use App\Models\Driver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZohoService
{
    protected $baseUrl = 'https://www.zohoapis.com/crm/v2';
    protected $accountsUrl = 'https://accounts.zoho.com/oauth/v2/token';
    protected $tokenPath = 'zoho/tokens.json';

    /**
     * Step 1: Exchange the Code for Tokens
     */
    public function generateTokens($grantToken)
    {
        $response = Http::asForm()->post($this->accountsUrl, [
            'code'          => $grantToken,
            'client_id'     => config('zoho.client_id'), // Updated to match your module config
            'client_secret' => config('zoho.client_secret'),
            'redirect_uri'  => config('zoho.redirect_uri'),
            'grant_type'    => 'authorization_code',
        ]);

        if ($response->successful()) {
            Storage::put($this->tokenPath, $response->body());
            return true;
        }

        throw new \Exception("Zoho Token Generation Failed: " . $response->body());
    }

    /**
     * Step 2: Get a fresh Access Token using the Refresh Token
     */
    public function refreshToken()
    {
        $fullPath = storage_path('app/zoho/tokens.json');

        if (!file_exists($fullPath)) {
        if (config('zoho.refresh_token')) {
            // Create the directory if it doesn't exist
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $initialTokens = [
                'refresh_token' => config('zoho.refresh_token'),
                'access_token'  => config('zoho.access_token'), // Optional
            ];
            file_put_contents($fullPath, json_encode($initialTokens));
        } else {
            throw new \Exception("Zoho Configuration missing: No token file and no ZOHO_REFRESH_TOKEN in ENV.");
        }
    }

        $content = file_get_contents($fullPath);
        $tokens = json_decode($content, true);

        // 1. Check if we have an access token and if it was updated in the last 50 minutes
        // (Zoho tokens last 60 minutes, so we use 50 to be safe)
        $lastModified = filemtime($fullPath);
        $isTokenFresh = (time() - $lastModified) < 3000; // 3000 seconds = 50 mins

        if (isset($tokens['access_token']) && $isTokenFresh) {
            return $tokens['access_token'];
        }

        // 2. ONLY if the token is old, actually call Zoho's API
        $response = Http::asForm()->post($this->accountsUrl, [
            'refresh_token' => $tokens['refresh_token'],
            'client_id'     => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'grant_type'    => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $tokens['access_token'] = $data['access_token'];
            
            file_put_contents($fullPath, json_encode($tokens));
            
            return $data['access_token'];
        }

        throw new \Exception("Zoho Token Refresh Failed: " . $response->body());
    }

    /**
     * Step 3: Fetch Drivers (Leads) with Pagination Support
     */
    public function getDriverById($zohoId)
    {
        $accessToken = $this->refreshToken();

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get("{$this->baseUrl}/Leads/{$zohoId}");

        return $response->json();
    }

    /**
     * Sync Zoho Leads into the local Users table
     */
    public function syncDrivers()
    {
        // 1. Fetch the data from Zoho (Contacts module)
        $results = $this->getDrivers(1); 
        $zohoContacts = $results['data'] ?? [];
        $updatedCount = 0;

        foreach ($zohoContacts as $contact) {
            // Get the phone or mobile from Zoho
            $zohoMobile = $contact['Mobile'] ?? $contact['Phone'] ?? null;

            if ($zohoMobile) {
                // Remove everything except digits (e.g., +63 995 -> 63995)
                $cleanZohoMobile = preg_replace('/[^0-9]/', '', $zohoMobile);

                // We look for a local driver where the phone_number contains these digits
                // We use the last 10 digits to be safe against country code variations
                $matchDigits = substr($cleanZohoMobile, -10);

                $driver = Driver::where('phone_number', 'LIKE', '%' . $matchDigits)->first();

                if ($driver) {
                    $driver->update([
                        'zoho_id' => $contact['id']
                    ]);
                    $updatedCount++;
                }
            }
        }

        return $updatedCount;
    }

    public function getDrivers($page = 1)
    {
        $accessToken = $this->refreshToken();

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get("{$this->baseUrl}/Contacts", [
            'page' => $page,
            'per_page' => 200 
        ]);

        return $response->json();
    }

    public function getDriverCount()
    {
        $accessToken = $this->refreshToken();

        // Zoho CRM Count API
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get("{$this->baseUrl}/Leads/actions/count");

        $data = $response->json();
        
        // Zoho returns an array of counts; we usually want the first one
        return $data[0]['count'] ?? ($data['count'] ?? 0);
    }

    public function getContactById($zohoId)
    {
        $accessToken = $this->refreshToken();

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get("{$this->baseUrl}/Contacts/{$zohoId}");

        return $response->json();
    }

    public function getFieldsMetadata()
    {
        $accessToken = $this->refreshToken();

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get("{$this->baseUrl}/settings/fields", [
            'module' => 'Contacts'
        ]);

        return $response->json();
    }

    public function getAttachments($zohoId)
    {
        $accessToken = $this->refreshToken(); 

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get("https://www.zohoapis.com/crm/v6/Contacts/{$zohoId}/Attachments");

        // Check if the request failed before doing anything else
        if ($response->failed()) {
            return [
                'error' => 'Zoho API unreachable',
                'status' => $response->status(),
                'details' => $response->body()
            ];
        }

        $data = $response->json();

        // The FIX: If Zoho returns no attachments, 'data' won't exist. 
        // We return an empty array instead of letting PHP crash.
        return [
            'data' => $data['data'] ?? [] 
        ];
    }

    public function downloadAttachment($contactId, $attachmentId)
    {
        $accessToken = $this->refreshToken();
        
        // Use v6 and check that there are no extra spaces in the ID
        $url = "https://www.zohoapis.com/crm/v6/Contacts/{$contactId}/Attachments/{$attachmentId}";

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get($url);

        // If you still get an error, let's log it to see what Zoho says
        if (!$response->successful()) {
            \Log::error("Zoho Download Error: " . $response->body());
            return null;
        }

        return [
            'content' => $response->body(),
            'type' => $response->header('Content-Type')
        ];
    }

    public function downloadFileField($contactId, $fileId)
    {
        $accessToken = $this->refreshToken();
        
        // This is the specific endpoint for File Upload fields (v6)
        $url = "https://www.zohoapis.com/crm/v6/Contacts/{$contactId}/actions/download_fields_attachment?fields_attachment_id={$fileId}";

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->get($url);

        if ($response->successful()) {
            return [
                'content' => $response->body(),
                'type' => $response->header('Content-Type')
            ];
        }

        return null;
    }
}