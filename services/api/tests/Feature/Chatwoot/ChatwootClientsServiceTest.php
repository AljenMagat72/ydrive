<?php

use App\Models\Clients\Client;
use App\Services\Chatwoot\ChatwootContactService;

use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\CreateContact;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\SearchContacts;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\UpdateContact;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\MergeContacts;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Labels\GetLabels;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Labels\UpdateLabels;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Notes\CreateNote;

use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

// -------------------------------------------------------------------------
// findOrCreate
// -------------------------------------------------------------------------

test('findOrCreate returns existing contact id without calling api', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => 'contact-123']);

    Saloon::fake([]);

    $result = app(ChatwootContactService::class)->findOrCreate($client);

    expect($result)->toBe('contact-123');
    Saloon::assertNothingSent();
});

test('findOrCreate creates contact when none exists', function () {
    $client = Client::factory()->createQuietly([
        'chatwoot_contact_id' => null,
    ]);

    Saloon::fake([
        SearchContacts::class => MockResponse::make(['payload' => []], 200),
        CreateContact::class => MockResponse::make(['payload' => ['contact' => ['id' => 'new-contact']]], 200),
        UpdateContact::class => MockResponse::make(['payload' => []], 200),
        UpdateContact::class => MockResponse::make(['payload' => []], 200),
    ]);

    app(ChatwootContactService::class)->findOrCreate($client);

    Saloon::assertSent(CreateContact::class);
});

// -------------------------------------------------------------------------
// updateOrCreate
// -------------------------------------------------------------------------

test('updateOrCreate calls update when contact id exists', function () {
    $client = Client::factory()->createQuietly([
        'chatwoot_contact_id' => 'contact-123',
        'avatar_url' => 'https://example.com/a.jpg'
    ]);

    Saloon::fake([
        UpdateContact::class => MockResponse::make(['payload' => []], 200),
    ]);

    app(ChatwootContactService::class)->updateOrCreate($client);

    Saloon::assertSent(function (UpdateContact $request) use ($client) {
        expect($request->resolveEndpoint())->toContain('/contacts/contact-123');
        expect($request->body()->get('name'))->toBe($client->name);
        expect($request->body()->get('avatar'))->toBe('https://example.com/a.jpg');

        return true;
    });
});

test('updateOrCreate calls create when no contact id', function () {
    $client = Client::factory()->createQuietly([
        'chatwoot_contact_id' => null,
    ]);

    Saloon::fake([
        SearchContacts::class => MockResponse::make(['payload' => []], 200),
        CreateContact::class => MockResponse::make(['payload' => ['contact' => ['id' => 'new-contact']]], 200),
        UpdateContact::class => MockResponse::make(['payload' => []], 200),
        UpdateContact::class => MockResponse::make(['payload' => []], 200),
    ]);

    app(ChatwootContactService::class)->updateOrCreate($client);

    Saloon::assertSent(CreateContact::class);
});

// -------------------------------------------------------------------------
// update
// -------------------------------------------------------------------------

test('update throws when client has no contact id', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => null]);

    expect(fn() => app(ChatwootContactService::class)->update($client))->toThrow(ErrorException::class);
});

test('update sends name and avatar to api', function () {
    $client = Client::factory()->createQuietly([
        'chatwoot_contact_id' => 'contact-123',
        'avatar_url' => 'https://example.com/avatar.jpg'
    ]);

    Saloon::fake([
        UpdateContact::class => MockResponse::make(['payload' => []], 200),
    ]);

    app(ChatwootContactService::class)->update($client);

    Saloon::assertSent(function (UpdateContact $request) use ($client) {
        expect($request->resolveEndpoint())->toContain('/contacts/contact-123');
        expect($request->body()->get('name'))->toBe($client->name);
        expect($request->body()->get('avatar'))->toBe('https://example.com/avatar.jpg');

        return true;
    });
});

// -------------------------------------------------------------------------
// delete / restore / block / unblock
// -------------------------------------------------------------------------

test('delete adds deactivated label', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => 'contact-123']);

    Saloon::fake([
        GetLabels::class => MockResponse::make(['payload' => ['vip']], 200),
        UpdateLabels::class => MockResponse::make(['payload' => ['vip', 'deactivated']], 200),
    ]);

    app(ChatwootContactService::class)->delete($client);

    Saloon::assertSent(function (UpdateLabels $request) {
        expect($request->resolveEndpoint())->toContain('/contacts/contact-123');
        expect($request->body()->get('payload'))->toBe(['vip', 'deactivated']);

        return true;
    });
});

test('restore removes deactivated label', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => 'contact-123']);

    Saloon::fake([
        GetLabels::class => MockResponse::make(['payload' => ['vip', 'deactivated']], 200),
        UpdateLabels::class => MockResponse::make(['payload' => ['vip']], 200),
    ]);

    app(ChatwootContactService::class)->restore($client);

    Saloon::assertSent(function (UpdateLabels $request) {
        expect($request->resolveEndpoint())->toContain('/contacts/contact-123');
        expect($request->body()->get('payload'))->toBe(['vip']);

        return true;
    });
});

test('block adds blocked label', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => 'contact-123']);

    Saloon::fake([
        GetLabels::class => MockResponse::make(['payload' => []], 200),
        UpdateLabels::class => MockResponse::make(['payload' => ['blocked']], 200),
    ]);

    app(ChatwootContactService::class)->block($client);

    Saloon::assertSent(function (UpdateLabels $request) {
        expect($request->resolveEndpoint())->toContain('/contacts/contact-123');
        expect($request->body()->get('payload'))->toBe(['blocked']);

        return true;
    });
});

test('unblock removes blocked label', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => 'contact-123']);

    Saloon::fake([
        GetLabels::class => MockResponse::make(['payload' => ['blocked', 'vip']], 200),
        UpdateLabels::class => MockResponse::make(['payload' => ['vip']], 200),
    ]);

    app(ChatwootContactService::class)->unblock($client);

    Saloon::assertSent(function (UpdateLabels $request) {
        expect($request->body()->get('payload'))->toBe(['vip']);

        return true;
    });
});

test('adding a label that already exists does not duplicate it', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => 'contact-123']);

    Saloon::fake([
        GetLabels::class => MockResponse::make(['payload' => ['blocked']], 200),
        UpdateLabels::class => MockResponse::make([]),
    ]);

    app(ChatwootContactService::class)->block($client);

    Saloon::assertSent(UpdateLabels::class);
});

// -------------------------------------------------------------------------
// migrateByPhone / migrateByEmail
// -------------------------------------------------------------------------

test('migrateByPhone throws when client has no contact id', function () {
    $client = Client::factory()->createQuietly(['chatwoot_contact_id' => null]);

    expect(fn() => app(ChatwootContactService::class)->migrateByPhone($client))->toThrow(ErrorException::class);
});

test('migrateByPhone clears old contact and assigns phone to new contact', function () {
    $client = Client::factory()->createQuietly([
        'chatwoot_contact_id' => 'contact-123',
        'phone_number' => '+15550001111'
    ]);

    Saloon::fake([
        SearchContacts::class => MockResponse::make(['payload' => [['id' => 'old-contact', 'phone_number' => '+15550001111']]], 200),
        UpdateContact::class => MockResponse::make(['payload' => []], 200),
        CreateNote::class => MockResponse::make(['payload' => []], 200),
    ]);

    app(ChatwootContactService::class)->migrateByPhone($client);

    Saloon::assertSent(SearchContacts::class);
    Saloon::assertSent(UpdateContact::class);
    Saloon::assertSent(CreateNote::class);
});

test('migrateByPhone skips migration when phone already belongs to same contact', function () {
    $client = Client::factory()->createQuietly([
        'chatwoot_contact_id' => 'contact-123',
        'phone_number' => '+15550001111'
    ]);

    Saloon::fake([
        SearchContacts::class => MockResponse::make(['payload' => [['id' => 'contact-123', 'phone_number' => '+15550001111']]], 200),
    ]);

    app(ChatwootContactService::class)->migrateByPhone($client);

    Saloon::assertNotSent(UpdateContact::class);
    Saloon::assertNotSent(CreateNote::class);
});

// -------------------------------------------------------------------------
// getLabels / updateLabels / searchContacts / mergeContacts
// -------------------------------------------------------------------------

test('getLabels returns payload from api', function () {
    Saloon::fake([
        GetLabels::class => MockResponse::make(['payload' => ['vip', 'blocked']], 200),
    ]);

    expect(app(ChatwootContactService::class)->getLabels('contact-123'))->toBe(['vip', 'blocked']);
});

test('getLabels returns empty array when payload is missing', function () {
    Saloon::fake([
        GetLabels::class => MockResponse::make([], 200),
    ]);

    expect(app(ChatwootContactService::class)->getLabels('contact-123'))->toBe([]);
});

test('updateLabels calls api with correct arguments', function () {
    Saloon::fake([
        UpdateLabels::class => MockResponse::make(['labels' => []], 200),
    ]);

    app(ChatwootContactService::class)->updateLabels('contact-123', ['vip']);

    Saloon::assertSent(function (UpdateLabels $request) {
        expect($request->resolveEndpoint())->toContain('/contacts/contact-123');
        expect($request->body()->get('payload'))->toBe(['vip']);

        return true;
    });
});

test('searchContacts passes query to api', function () {
    Saloon::fake([
        SearchContacts::class => MockResponse::make(['payload' => []], 200),
    ]);

    app(ChatwootContactService::class)->searchContacts(['q' => 'john']);

    Saloon::assertSent(function (SearchContacts $request) {
        expect($request->query()->get('q'))->toBe('john');

        return true;
    });
});

test('mergeContacts calls api with base and merge ids', function () {
    Saloon::fake([
        MergeContacts::class => MockResponse::make(['payload' => []], 200),
    ]);

    app(ChatwootContactService::class)->mergeContacts('base-id', 'merge-id');

    Saloon::assertSent(function ($request) {
        expect($request->body()->get('base_contact_id'))->toBe('base-id');
        expect($request->body()->get('mergee_contact_id'))->toBe('merge-id');

        return true;
    });
});
