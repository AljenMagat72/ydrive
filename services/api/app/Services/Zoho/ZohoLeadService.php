<?php

namespace App\Services\Zoho;

use App\Http\Integrations\Zoho\ZohoApi;
use App\Models\Clients\Client;

/**
 * Portfolio mock: syncs rider leads into Zoho CRM Leads module.
 */
class ZohoLeadService
{
    public function __construct(protected ZohoApi $zohoApi)
    {
    }

    public function upsertFromClient(Client $client): ?string
    {
        $response = $this->zohoApi->records('Leads')->upsert([
            [
                'First_Name' => $client->first_name,
                'Last_Name' => $client->last_name ?: 'Rider',
                'Email' => $client->email,
                'Phone' => $client->phone_number,
                'Lead_Source' => 'YDrive App',
                'Company' => 'YDrive Rider',
                'Description' => "Autofleet client: {$client->autofleet_client_id}",
            ],
        ], ['Email', 'Phone']);

        return $response->json('data.0.details.id');
    }

    public function markConverted(string $leadId, string $riderId): void
    {
        $this->zohoApi->records('Leads')->upsert([
            [
                'id' => $leadId,
                'Converted_Rider_ID' => $riderId,
                'Lead_Status' => 'Converted',
            ],
        ], ['id']);
    }
}
