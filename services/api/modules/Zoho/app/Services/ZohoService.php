<?php

namespace Modules\Zoho\Services;

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

    public function updateContact(string $zohoId, array $data): array
    {
        $response = Http::withToken($this->refreshToken())
            ->put("{$this->baseUrl}/Contacts/{$zohoId}", [
                'data' => [$data]
            ]);

        return $response->json();
    }

public function uploadToFileField(string $recordId, string $fieldName, $file): array
{
    $token = $this->refreshToken();
    
    // STEP 1: Upload to ZFS (Zoho File System)
    $zfsUrl = "https://www.zohoapis.com/crm/v6/files";

    try {
        $uploadResponse = Http::withToken($token)
            ->attach(
                'file', 
                file_get_contents($file->getRealPath()), 
                $file->getClientOriginalName()
            )
            ->post($zfsUrl);

        $uploadData = $uploadResponse->json();

        if (!$uploadResponse->successful() || !isset($uploadData['data'][0]['details']['id'])) {
            Log::error("ZFS Upload Failed", ['res' => $uploadData]);
            return ['success' => false, 'message' => 'File rejected by storage.'];
        }

        $fileId = $uploadData['data'][0]['details']['id'];

        // STEP 2: Link File ID to the CONTACT Record
        // Using 'Contacts' as confirmed by your URL
        $updateUrl = "https://www.zohoapis.com/crm/v6/Contacts/{$recordId}";
        
        $updateResponse = Http::withToken($token)->put($updateUrl, [
            'data' => [
                [
                    // This specific nested array structure is mandatory for V6+
                    $fieldName => [
                        ['File_Id__s' => $fileId] 
                    ]
                ]
            ]
        ]);

        $updateData = $updateResponse->json();

        if ($updateResponse->successful() && ($updateData['data'][0]['code'] ?? '') === 'SUCCESS') {
            return ['success' => true, 'data' => $updateData];
        }

        // If it still says INVALID_DATA here, we should check the field API name
        Log::error("Zoho Update Failed", ['res' => $updateData]);
        return [
            'success' => false, 
            'message' => $updateData['data'][0]['message'] ?? 'Record update failed.'
        ];

    } catch (\Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
    public function addAttachment(string $contactId, $file, string $type = 'Document'): array
    {
        $token = $this->refreshToken();
        $url = "{$this->baseUrl}/Contacts/{$contactId}/Attachments";

        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_Hi');
        $cleanType = str_replace(' ', '_', $type);
        $safeName = "Update_{$cleanType}_{$timestamp}.{$extension}";

        $fileStream = fopen($file->getRealPath(), 'r');

        try {
            $response = Http::withToken($token)
                ->attach(
                    'file', 
                    $fileStream, 
                    $safeName 
                )
                ->post($url);

            if (is_resource($fileStream)) fclose($fileStream);

            $result = $response->json();

            if ($response->successful() && isset($result['data'][0]['code']) && $result['data'][0]['code'] === 'SUCCESS') {
                return ['success' => true, 'data' => $result];
            }

            Log::error("Zoho Attachment Rejected", [
                'contact' => $contactId,
                'response' => $result
            ]);

            return [
                'success' => false, 
                'message' => $result['data'][0]['message'] ?? 'Zoho rejected the attachment.'
            ];
        } catch (Exception $e) {
            if (is_resource($fileStream)) fclose($fileStream);
            return ['success' => false, 'message' => $e->getMessage()];
        }
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

    public function updateDriverRecord(string $zohoId, array $data): array
    {
        $response = Http::withToken($this->refreshToken())
            ->put("{$this->baseUrl}/Drivers/{$zohoId}", [
                'data' => [$data]
            ]);

        return $response->json();
    }
}