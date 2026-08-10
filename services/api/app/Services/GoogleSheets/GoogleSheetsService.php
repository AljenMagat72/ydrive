<?php

namespace App\Services\GoogleSheets;

use App\Http\Integrations\GoogleSheets\GoogleSheetsApi;
use App\Models\Clients\Client;

/**
 * Portfolio mock: reads/writes operational data to Google Sheets.
 */
class GoogleSheetsService
{
    public function __construct(protected GoogleSheetsApi $sheetsApi)
    {
    }

    public function spreadsheetId(): string
    {
        return (string) config('services.google_sheets.spreadsheet_id', 'mock-spreadsheet-id');
    }

    public function getRows(string $range = 'Sheet1!A:Z'): array
    {
        $response = $this->sheetsApi->values($this->spreadsheetId())->get($range);

        return $response->json('values') ?? [];
    }

    public function appendRows(array $rows, string $range = 'Sheet1!A:Z'): array
    {
        $response = $this->sheetsApi->values($this->spreadsheetId())->append($range, $rows);

        return $response->json() ?? [];
    }

    public function logClientSignup(Client $client): array
    {
        return $this->appendRows([
            [
                now()->toDateTimeString(),
                $client->id,
                $client->first_name,
                $client->last_name,
                $client->email,
                $client->phone_number,
                $client->autofleet_client_id,
                $client->device_type,
            ],
        ], config('services.google_sheets.ranges.signups', 'Signups!A:H'));
    }

    public function exportRideSummary(array $ride): array
    {
        return $this->appendRows([
            [
                $ride['id'] ?? null,
                $ride['status'] ?? null,
                $ride['pickup'] ?? null,
                $ride['dropoff'] ?? null,
                $ride['fare'] ?? null,
                $ride['completed_at'] ?? now()->toDateTimeString(),
            ],
        ], config('services.google_sheets.ranges.rides', 'Rides!A:F'));
    }
}
