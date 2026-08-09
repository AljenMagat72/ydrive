<?php

namespace App\Observers\Chatwoot;

use App\Jobs\Chatwoot\BlockChatwootClient;
use App\Jobs\Chatwoot\DeactivateChatwootClient;
use App\Jobs\Chatwoot\MigrateChatwootContact;
use App\Jobs\Chatwoot\ReactivateChatwootClient;
use App\Jobs\Chatwoot\SyncChatwootContact;
use App\Jobs\Chatwoot\UnblockChatwootClient;
use App\Models\Clients\Client;

class ChatwootClientObserver
{
    protected array $syncKeys = [
        'first_name',
        'last_name',
        'avatar',
    ];

    public function __construct()
    {
    }

    public function created(Client $client): void
    {
        SyncChatwootContact::dispatch($client);
    }

    public function updated(Client $client): void
    {
        $dirtyKeys = collect($client->getDirty())->keys();

        if ($client->chatwoot_contact_id === null || $dirtyKeys->intersect($this->syncKeys)->isNotEmpty()) {
            SyncChatwootContact::dispatch($client);
        }

        if ($dirtyKeys->contains('email') || $dirtyKeys->contains('email_verified_at')) {
            MigrateChatwootContact::dispatch($client);
        }

        if ($dirtyKeys->contains('is_active')) {
            $client->is_active ? UnblockChatwootClient::dispatch($client) : BlockChatwootClient::dispatch($client);
        }
    }

    public function deleted(Client $client): void
    {
        DeactivateChatwootClient::dispatch($client);
    }

    public function restored(Client $client): void
    {
        ReactivateChatwootClient::dispatch($client);
    }
}
