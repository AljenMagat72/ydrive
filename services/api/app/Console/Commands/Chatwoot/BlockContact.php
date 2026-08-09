<?php

namespace App\Console\Commands\Chatwoot;

use App\Models\Clients\Client;
use App\Services\Chatwoot\ChatwootContactService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('chatwoot:block-client')]
#[Description('Block a client in chatwoot')]
class BlockContact extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ChatwootContactService $chatwootClients)
    {
        $id = $this->argument('id');

        $client = Client::whereUuid($id)->withTrashed()->firstOrFail();

        $chatwootClients->block($client);
    }
}
