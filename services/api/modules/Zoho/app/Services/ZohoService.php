<?php

namespace Modules\Zoho\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class ZohoService
{
    protected string $baseUrl = 'https://www.zohoapis.com/crm/v6';
    protected string $accountsUrl = 'https://accounts.zoho.com/oauth/v2/token';
    protected string $tokenPath = 'zoho/tokens.json';

    /**
     * Refresh and return Access Token.
     */
    public function refreshToken(): string
    {
        if (!Storage::exists($this->tokenPath)) {
            $refreshToken = config('zoho.refresh_token');
            if (!$refreshToken) throw new Exception("ZOHO_REFRESH_TOKEN missing in ENV.");
            
            Storage::put($this->tokenPath, json_encode(['refresh_token' => $refreshToken]));
        }

        $tokens = json_decode(Storage::get($this->tokenPath), true);
        $lastModified = Storage::lastModified($this->tokenPath);

        if (isset($tokens['access_token']) && (time() - $lastModified) < 3000) {
            return $tokens['access_token'];
        }

        $response = Http::asForm()->post($this->accountsUrl, [
            'refresh_token' => $tokens['refresh_token'],
            'client_id'     => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'grant_type'    => 'refresh_token',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $tokens['access_token'] = $data['access_token'];
            Storage::put($this->tokenPath, json_encode($tokens));
            return $data['access_token'];
        }

        Log::error("Zoho Token Refresh Failed", $response->json());
        throw new Exception("Zoho Token Refresh Failed.");
    }

    /**
     * Download file using v6 primary and v2 fallback.
     */
    public function downloadFileField(string $contactId, string $fileId): ?array
    {
        $token = $this->refreshToken();

        //Field Attachment API
        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/Contacts/{$contactId}/actions/download_fields_attachment", [
                'fields_attachment_id' => $fileId
            ]);

        if ($response->successful() && !str_contains($response->header('Content-Type'), 'application/json')) {
            return [
                'content' => $response->body(),
                'type'    => $response->header('Content-Type')
            ];
        }

        //v2 Files API Fallback
        $v2Response = Http::withToken($token)
            ->get("https://www.zohoapis.com/crm/v2/files", ['id' => $fileId]);

        if ($v2Response->successful()) {
            return [
                'content' => $v2Response->body(),
                'type'    => $v2Response->header('Content-Type')
            ];
        }

        Log::error("Zoho Download Failed All Strategies", ['file_id' => $fileId]);
        return null;
    }

    public function getContactById(string $zohoId): array
    {
        return Http::withToken($this->refreshToken())
            ->get("{$this->baseUrl}/Contacts/{$zohoId}")
            ->json();
    }
}