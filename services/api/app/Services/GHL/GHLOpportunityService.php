<?php

namespace App\Services\GHL;

use App\Http\Integrations\GHL\GHLApi;
use Illuminate\Support\Facades\Http;

/**
 * Portfolio mock: creates pipeline opportunities in GoHighLevel.
 */
class GHLOpportunityService
{
    public function __construct(protected GHLApi $ghlApi)
    {
    }

    public function createForContact(string $contactId, string $name, float $monetaryValue = 0): array
    {
        // Lightweight mock wrapper around GHL opportunities endpoint.
        $response = Http::withToken(config('services.ghl.api_key', 'mock-ghl-api-key'))
            ->withHeaders([
                'Version' => config('services.ghl.api_version', '2021-07-28'),
            ])
            ->post(rtrim(config('services.ghl.base_url'), '/') . '/opportunities/', [
                'locationId' => config('services.ghl.location_id'),
                'pipelineId' => config('services.ghl.pipeline_id'),
                'pipelineStageId' => config('services.ghl.pipeline_stage_id'),
                'contactId' => $contactId,
                'name' => $name,
                'status' => 'open',
                'monetaryValue' => $monetaryValue,
            ]);

        return $response->json() ?? [
            'opportunity' => [
                'id' => 'mock-opportunity-' . substr(md5($contactId . $name), 0, 8),
                'contactId' => $contactId,
                'name' => $name,
                'status' => 'open',
            ],
        ];
    }
}
