<?php

namespace App\Observers\Zoho;

use App\Jobs\Chatwoot\MigrateChatwootContact;
use App\Jobs\Chatwoot\SyncChatwootContact;
use App\Jobs\Zoho\SyncZohoRider;
use App\Models\Clients\Client;
use App\Services\Zoho\ZohoRiderService;

class ZohoClientObserver
{
    protected array $syncKeys = [
        'first_name',
        'last_name',
        'avatar_url',
    ];

    public function __construct(protected ZohoRiderService $riders)
    {
    }

    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        SyncZohoRider::dispatch($client);
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        $dirtyKeys = collect($client->getDirty())->keys();

        if($client->zoho_rider_id === null || $dirtyKeys->intersect($this->syncKeys)->isNotEmpty()) {
            SyncZohoRider::dispatch($client);
        }
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }
}
