<?php

namespace App\Http\Controllers\AutoFleet;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Services\Autofleet\AutofleetClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutofleetClientWebHookController extends Controller
{
    public function __construct(protected AutofleetClientService $autofleetClients)
    {

    }

    public function onboarded(Request $request)
    {
        $client = $request->json('client');
        $this->autofleetClients->upsertFromAutofleet($client);
    }

    public function updated(Request $request)
    {
        $client = $request->json('client');
        Log::info($client);
        $this->autofleetClients->upsertFromAutofleet($client);
    }

    public function deleted(Request $request)
    {
        $id = $request->json('client.id');
        Log::info($id);
        Client::whereAutofleetClientId($id)->firstOrFail()->delete();
    }
}
