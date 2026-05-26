<?php

namespace App\Services\Autofleet;

use App\Http\Integrations\Autofleet\AutofleetApi;
use App\Models\Clients\Client;
use Carbon\Carbon;

class AutofleetClientService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AutofleetApi $autofleetApi)
    {
        //
    }

    public function getClientByUuid(string $uuid)
    {
        $response = $this->autofleetApi->clients()->get($uuid);
        return $response->json();
    }

    public function getClientByPhoneNumber(string $phoneNumber)
    {
        $clients = $this->queryClients($phoneNumber);

        return array_find($clients, fn($client) => ($client['phoneNumber'] ?? null) === $phoneNumber);
    }

    public function queryClients(string $searchTerm, int $page = 1)
    {
        $response = $this->autofleetApi->clients()->query($searchTerm, $page);

        return $response->json('rows', []);
    }

    public function upsertFromAutofleet(array $data) : Client
    {
        $isEmailVerified = filter_var($data['isEmailVerified'], FILTER_VALIDATE_BOOLEAN);

        return Client::updateOrCreate(
            [
                'uuid' => $data['id'],
            ],
            [
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'phone_number' => "+{$data['phoneNumber']}",
                'is_active' => $data['active'],
                'created_at' => Carbon::parse($data['createdAt']),
                'email' => $data['email'],
                'email_verified_at' => $isEmailVerified ? Carbon::now() : null,
                'avatar_url' => $data['avatar'],
                'autofleet_client_id' => $data['id'],
                'device_type' => $data['deviceType'],
            ]
        );
    }
}
