<?php

namespace App\Console\Commands\Chatwoot;

use App\Jobs\Chatwoot\DeactivateChatwootClient;
use App\Models\Clients\Client;
use App\Services\Chatwoot\ChatwootContactService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('chatwoot:deactivate-client {id}')]
#[Description('Deactivate a client')]
class DeactivateContact extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ChatwootContactService $chatwootClients)
    {
        $id = $this->argument('id');

        $client = Client::whereUuid($id)->withTrashed()->firstOrFail();

        $chatwootClients->delete($client);
    }
}
