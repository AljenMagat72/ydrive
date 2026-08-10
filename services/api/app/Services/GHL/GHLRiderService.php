<?php

namespace App\Services\GHL;

use App\Http\Integrations\GHL\GHLApi;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\Cache;

/**
 * Portfolio mock: syncs riders into GoHighLevel contacts.
 */
class GHLRiderService
{
    public function __construct(protected GHLApi $ghlApi)
    {
    }

    public function findOrCreate(Client $client): ?string
    {
        return Cache::withoutOverlapping("ghl:rider:{$client->id}:create", function () use ($client) {
            $existing = $this->findByPhone($client->phone_number);

            if ($existing) {
                return $existing;
            }

            return $this->upsert($client);
        });
    }

    public function updateOrCreate(Client $client): ?string
    {
        return $this->upsert($client);
    }

    public function findByPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $response = $this->ghlApi->contacts()->search([
            'query' => $phone,
        ]);

        return $response->json('contacts.0.id');
    }

    protected function upsert(Client $client): ?string
    {
        $response = $this->ghlApi->contacts()->upsert([
            'firstName' => $client->first_name,
            'lastName' => $client->last_name,
            'email' => $client->email,
            'phone' => $client->phone_number,
            'source' => 'YDrive App',
            'tags' => ['rider', 'ydrive'],
            'customFields' => [
                [
                    'key' => 'autofleet_client_id',
                    'field_value' => (string) $client->autofleet_client_id,
                ],
                [
                    'key' => 'device_type',
                    'field_value' => (string) $client->device_type,
                ],
            ],
        ]);

        return $response->json('contact.id') ?? $response->json('id');
    }
}
