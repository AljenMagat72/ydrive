<?php

namespace App\Jobs\Chatwoot;

use App\Models\Clients\Client;
use App\Services\Chatwoot\ChatwootContactService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeactivateChatwootClient implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Client $client)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChatwootContactService $clients): void
    {
        $clients->delete($this->client);
    }
}
