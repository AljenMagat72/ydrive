<?php

namespace App\Services\Zoho;

use App\Http\Integrations\Zoho\ZohoApi;
use App\Models\Clients\Client;
use Cache;

class ZohoRiderService
{
    public function __construct(protected ZohoApi $zohoApi)
    {
    }

    public function findOrCreate(Client $client)
    {
        return Cache::withoutOverlapping("zoho:rider:{$client->id}:create", function () use ($client) {
            if ($client->fresh()->zoho_rider_id) {
                return $client->zoho_rider_id;
            }

            $zohoId = $this->updateOrCreate($client);

            return $zohoId;
        });
    }

    public function updateOrCreate(Client $client)
    {
        $id = $this->upsert($client);

        if (!$client->zoho_rider_id) {

            $client->updateQuietly([
                'zoho_rider_id' => $id,
            ]);
        }

        return $id;
    }

    protected function upsert(Client $client)
    {
        $response = $this->zohoApi->records('Riders')->upsert([
            [
                'Name' => "{$client->first_name}",
                'Last_Name' => "{$client->last_name}",
                'Phone_1' => "{$client->phone_number}",
                'AutoFleet_ID' => "{$client->autofleet_client_id}",
                'Device_Type' => "{$client->device_type}",
                'Email' => "{$client->email}",
                'Is_Email_Verified' => $client->email_verified_at != null,
                'Is_Active' => $client->is_active,
                'Created_At' => "{$client->created_at}",
            ]
        ], ['AutoFleet_ID']);

        $id = $response->json('data.0.details.id');

        return $id;
    }
}
