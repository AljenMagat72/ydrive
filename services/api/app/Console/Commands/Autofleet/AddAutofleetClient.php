<?php

namespace App\Console\Commands\Autofleet;

use App\Services\Autofleet\AutofleetClientService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('autofleet:add-client {--id= : Search and add by Autofleet client UUID} {--phone= : Search and add by phone number}')]
#[Description('Fetch a client from Autofleet and store them locally by ID or Phone Number')]
class AddAutofleetClient extends Command
{
    public function handle(AutofleetClientService $service)
    {
        $id = $this->option('id');
        $phone = $this->option('phone');


        if (!$id && !$phone) {
            $this->error('You must provide either an --id= or a --phone= option.');
            return self::FAILURE;
        }

        if ($id && $phone) {
            $this->error('Please provide either --id OR --phone, not both.');
            return self::FAILURE;
        }
        $clientData = $id
            ? $service->getClientByUuid($id)
            : $service->getClientByPhoneNumber($phone);

        if (!$clientData) {
            return self::FAILURE;
        }

        $client = $service->upsertFromAutofleet($clientData);

        $this->info("Successfully added client: {$client->first_name} {$client->last_name}");
        return self::SUCCESS;
    }
}
