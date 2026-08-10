<?php

namespace App\Services\Airtable;

use App\Http\Integrations\Airtable\AirtableApi;
use App\Models\Clients\Client;

/**
 * Portfolio mock: CRM-style rider/driver sync into Airtable bases.
 */
class AirtableService
{
    public function __construct(protected AirtableApi $airtableApi)
    {
    }

    public function ridersTable(): string
    {
        return (string) config('services.airtable.tables.riders', 'Riders');
    }

    public function driversTable(): string
    {
        return (string) config('services.airtable.tables.drivers', 'Drivers');
    }

    public function listRiders(array $query = []): array
    {
        $response = $this->airtableApi->records($this->ridersTable())->list($query);

        return $response->json('records') ?? [];
    }

    public function upsertRider(Client $client): ?string
    {
        $existing = $this->findRiderByPhone($client->phone_number);

        $fields = [
            'First Name' => $client->first_name,
            'Last Name' => $client->last_name,
            'Email' => $client->email,
            'Phone' => $client->phone_number,
            'Autofleet ID' => (string) $client->autofleet_client_id,
            'Device Type' => (string) $client->device_type,
            'Active' => (bool) $client->is_active,
        ];

        if ($existing) {
            $response = $this->airtableApi->records($this->ridersTable())->update($existing, $fields);

            return $response->json('id') ?? $existing;
        }

        $response = $this->airtableApi->records($this->ridersTable())->create([
            ['fields' => $fields],
        ]);

        return $response->json('records.0.id');
    }

    public function findRiderByPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $escaped = str_replace("'", "\\'", $phone);
        $response = $this->airtableApi->records($this->ridersTable())->list([
            'filterByFormula' => "{Phone} = '{$escaped}'",
            'maxRecords' => 1,
        ]);

        return $response->json('records.0.id');
    }

    public function createDriverApplication(array $payload): ?string
    {
        $response = $this->airtableApi->records($this->driversTable())->create([
            [
                'fields' => [
                    'Full Name' => $payload['name'] ?? null,
                    'Email' => $payload['email'] ?? null,
                    'Phone' => $payload['phone'] ?? null,
                    'City' => $payload['city'] ?? null,
                    'Status' => $payload['status'] ?? 'New',
                    'Source' => 'YDrive Portal',
                ],
            ],
        ]);

        return $response->json('records.0.id');
    }
}
