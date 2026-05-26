<?php

use App\Http\Integrations\Zoho\Requests\Records\UpsertRecords;
use App\Models\Clients\Client;
use App\Services\Zoho\ZohoRiderService;

use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

// -------------------------------------------------------------------------
// findOrCreate
// -------------------------------------------------------------------------

beforeEach(function() {
    Cache::set('zoho:access-token', 'fake-token');
});

test('findOrCreate returns existing contact id without calling api', function () {
    $client = Client::factory()->createQuietly(['zoho_rider_id' => 'zoho-123']);

    Saloon::fake([]);

    $result = app(ZohoRiderService::class)->findOrCreate($client);

    expect($result)->toBe('zoho-123');
    Saloon::assertNothingSent();
});

test('findOrCreate creates contact when none exists', function () {
    $client = Client::factory()->createQuietly([
        'zoho_rider_id' => null,
    ]);

    Saloon::fake([
        UpsertRecords::class => MockResponse::make([
            'data' => [['details' => ['id' => 'new-zoho-id']]]
        ], 200),
    ]);

    app(ZohoRiderService::class)->findOrCreate($client);

    Saloon::assertSent(UpsertRecords::class);
});

// -------------------------------------------------------------------------
// updateOrCreate
// -------------------------------------------------------------------------

test('updateOrCreate calls update when contact id exists', function () {
    $client = Client::factory()->createQuietly([
        'zoho_rider_id' => 'zoho-123',
    ]);

    Saloon::fake([
        UpsertRecords::class => MockResponse::make([
            'data' => [['details' => ['id' => 'zoho-123']]]
        ], 200),
    ]);

    app(ZohoRiderService::class)->updateOrCreate($client);

    Saloon::assertSent(UpsertRecords::class);
});

test('updateOrCreate calls create when no contact id', function () {
    $client = Client::factory()->createQuietly([
        'zoho_rider_id' => null,
    ]);

    Saloon::fake([
        UpsertRecords::class => MockResponse::make([
            'data' => [['details' => ['id' => 'new-zoho-id']]]
        ], 200),
    ]);

    app(ZohoRiderService::class)->updateOrCreate($client);

    Saloon::assertSent(UpsertRecords::class);
});
