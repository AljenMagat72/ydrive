<?php

namespace Modules\Zoho\Services;

use ZipArchive;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ZohoService
{
    protected string $baseUrl = 'https://www.zohoapis.com/crm/v6';
    protected string $accountsUrl = 'https://accounts.zoho.com/oauth/v2/token';
    protected string $tokenPath = 'zoho/tokens.json';

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

    public function getContactById(string $zohoId): array
    {
        return Http::withToken($this->refreshToken())
            ->get("{$this->baseUrl}/Contacts/{$zohoId}")
            ->json();
    }

    public function downloadFileField(string $contactId, string $fileId): ?array
    {
        $token = $this->refreshToken();
        $url = "{$this->baseUrl}/Contacts/{$contactId}/actions/download_fields_attachment";
        $response = Http::withToken($token)->get($url, ['fields_attachment_id' => $fileId]);

        if (!$response->successful()) {
            $fallbackUrl = "https://www.zohoapis.com/crm/v2/files";
            $response = Http::withToken($token)->get($fallbackUrl, ['id' => $fileId]);
        }

        if ($response->successful()) {
            $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
            $disposition = $response->header('Content-Disposition') ?? '';
            
            preg_match('/filename="(.+)"/', $disposition, $matches);
            $originalName = $matches[1] ?? "file_{$fileId}";

            $ext = 'bin'; 
            if (str_contains($contentType, 'image/png')) $ext = 'png';
            elseif (str_contains($contentType, 'image/jp')) $ext = 'jpg';
            elseif (str_contains($contentType, 'application/pdf')) $ext = 'pdf';
            elseif (str_contains($contentType, 'wordprocessingml')) $ext = 'docx';

            $cleanName = preg_replace('/\.(bin|file|attachment)$/i', '', $originalName);
            $finalName = !str_contains($cleanName, '.') ? "{$cleanName}.{$ext}" : $cleanName;

            return [
                'content' => $response->body(),
                'type'    => $contentType,
                'name'    => $finalName 
            ];
        }
        return null;
    }
}