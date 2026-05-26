<?php

namespace App\Jobs\Zoho;

use App\Models\Clients\Client;
use App\Services\Zoho\ZohoRiderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncZohoRider implements ShouldQueue
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
    public function handle(ZohoRiderService $riders): void
    {
        $riders->updateOrCreate($this->client);
    }
}
