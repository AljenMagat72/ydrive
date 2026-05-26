<?php

namespace App\Services\Chatwoot;

use App\Http\Integrations\ChatwootPlatforms\ChatwootPlatformsApi;
use App\Models\Clients\Client;
use Cache;
use ErrorException;

class ChatwootContactService
{
    public function __construct(protected ChatwootPlatformsApi $chatwootApi)
    {
    }

    public function findOrCreate(Client $client)
    {
        if ($client->chatwoot_contact_id) {
            return $client->chatwoot_contact_id;
        }

        return $this->create($client);
    }

    public function updateOrCreate(Client $client)
    {
        if ($client->chatwoot_contact_id) {
            return $this->update($client);
        }

        $this->create($client);
    }

    public function update(Client $client)
    {
        if (!$client->chatwoot_contact_id) {
            throw new ErrorException();
        }

        $this->updateContact($client->chatwoot_contact_id, [
            'name' => $client->name,
            'avatar' => $client->avatar_url,
        ]);
    }

    protected function findByIdentifier(string $identifier)
    {
        $response = $this->searchContacts([
            'q' => $identifier,
        ]);

        $foundIdentifier = $response->json('payload.0.identifier');

        if ($identifier !== $foundIdentifier) {
            return null;
        }

        return $response->json('payload.0');
    }

    protected function findByPhoneNumber(string $phoneNumber)
    {
        $response = $this->searchContacts([
            'q' => $phoneNumber,
        ]);

        $foundPhoneNumber = $response->json('payload.0.phone_number');

        if ($phoneNumber !== $foundPhoneNumber) {
            return null;
        }

        return $response->json('payload.0');
    }

    protected function findByEmail(string $email)
    {
        $response = $this->searchContacts([
            'q' => $email,
        ]);

        $foundEmail = $response->json('payload.0.email');

        if ($email !== $foundEmail) {
            return null;
        }

        return $response->json('payload.0');
    }

    public function create(Client $client)
    {
        $contactId = Cache::withoutOverlapping("chatwoot:contact:{$client->id}:create", function () use ($client) {
            if ($client->fresh()->chatwoot_contact_id !== null) {
                return $client->chatwoot_contact_id;
            }

            $contact = $this->findByIdentifier($client->uuid);

            if (!$contact) {
                $contact = $this->mergeOrNew($client);
            }
            $contactId = data_get($contact, 'id');

            $client->updateQuietly([
                'chatwoot_contact_id' => $contactId,
            ]);

            $this->migrate($client);

            return $contactId;
        });

        return $contactId;
    }

    protected function mergeOrNew(Client $client)
    {
        $phoneContact = $this->findByPhoneNumber($client->phone_number);
        $emailContact = $this->findByEmail($client->email);

        $phoneContactIdentifier = data_get($phoneContact, 'identifier');
        $isPhoneUsable = $phoneContact && !$phoneContactIdentifier;

        $emailContactIdentifier = data_get($emailContact, 'identifier');
        $isEmailUsable = $emailContact
            && !$emailContactIdentifier
            && $client->email_verified_at !== null;

        $contactId = null;

        if ($isPhoneUsable && $isEmailUsable) {
            $this->mergeContacts($phoneContact, $emailContact);
            $contactId = data_get($phoneContact, 'id');
        }

        if ($isPhoneUsable) {
            $contactId = data_get($phoneContact, 'id');
        }

        if ($isEmailUsable) {
            $contactId = data_get($emailContact, 'id');
        }

        $clientData = [
            'name' => $client->name,
            'identifier' => $client->uuid,
            'avatar_url' => $client->avatar,
        ];

        $response = $contactId ? $this->updateContact($contactId, $clientData) : $this->createContact($clientData);
        return $response->json('payload.contact');
    }

    public function delete(Client $client)
    {
        $this->addLabel($client, 'deactivated');
    }

    public function restore(Client $client)
    {
        $this->removeLabel($client, 'deactivated');
    }

    public function block(Client $client)
    {
        $this->addLabel($client, 'blocked');
    }

    public function unblock(Client $client)
    {
        $this->removeLabel($client, 'blocked');
    }

    public function migrate(Client $client)
    {
        $this->migrateByPhone($client);
        $this->migrateByEmail($client);
    }

    public function migrateByPhone(Client $client)
    {
        if (!$client->chatwoot_contact_id) {
            throw new ErrorException();
        }

        Cache::withoutOverlapping("chatwoot:contact:{$client->id}:migrate-phone", function () use ($client) {
            $phoneContact = $this->findByPhoneNumber($client->phone_number);

            $phoneContactId = data_get($phoneContact, 'id');

            if ($phoneContactId === $client->chatwoot_contact_id) {
                return;
            }

            if ($phoneContactId !== null) {
                $this->updateContact($phoneContactId, ['phone_number' => null]);
                $this->createMigrationNote(
                    $phoneContactId,
                    $client->chatwoot_contact_id
                );
            }

            $this->updateContact($client->chatwoot_contact_id, ['phone_number' => $client->phone_number]);
        });
    }

    public function migrateByEmail(Client $client)
    {
        if (!$client->chatwoot_contact_id) {
            throw new ErrorException();
        }

        Cache::withoutOverlapping("chatwoot:contact:{$client->id}:migrate-email", function () use ($client) {
            $emailContactId = $this->findByEmail($client->email);
            if ($emailContactId === $client->chatwoot_contact_id || ($emailContactId !== null && $client->email_verified_at === null)) {
                return;
            }

            if ($emailContactId !== null) {
                $this->updateContact($emailContactId, ['email' => null]);
                $this->createMigrationNote(
                    $emailContactId,
                    $client->chatwoot_contact_id
                );
            }

            $this->updateContact($client->chatwoot_contact_id, ['email' => $client->email]);
        });
    }

    protected function createMigrationNote(string $previousContactId, string $newContactId)
    {
        $accountId = config('services.chatwoot.clients.account_id');
        $baseUrl = config('services.chatwoot.base_url');

        $contactToUrl = "{$baseUrl}/{$accountId}/{$newContactId}";
        $contactFromUrl = "{$baseUrl}/{$accountId}/{$previousContactId}";

        $noteTo = "Migrated to [{$contactToUrl}]({$newContactId})";
        $noteFrom = "Migrated from [{$contactFromUrl}]({$previousContactId})";

        $this->createContactNote($previousContactId, $noteTo);
        $this->createContactNote($newContactId, $noteFrom);
    }

    protected function addLabel(Client $client, string $label)
    {
        if (!$client->chatwoot_contact_id) {
            throw new ErrorException();
        }

        $labels = $this->getLabels($client->chatwoot_contact_id);

        $newLabels = array_values(array_unique([...$labels, $label]));

        $this->updateLabels($client->chatwoot_contact_id, $newLabels);

        return $newLabels;
    }

    protected function removeLabel(Client $client, string $label)
    {
        if (!$client->chatwoot_contact_id) {
            throw new ErrorException();
        }

        $labels = $this->getLabels($client->chatwoot_contact_id);

        $newLabels = array_values(array_filter($labels, fn($l) => $l !== $label));

        $this->updateLabels($client->chatwoot_contact_id, $newLabels);

        return $newLabels;
    }

    protected function contacts()
    {
        return $this
            ->chatwootApi
            ->contacts(config('services.chatwoot.clients.account_id'));
    }

    protected function createContact(array $data)
    {
        return $this
            ->contacts()
            ->create($data);
    }

    protected function createContactNote(string $contactId, string $note)
    {
        return $this
            ->contacts()
            ->notes()
            ->create($contactId, $note);
    }

    protected function updateContact(string $contactId, array $data)
    {
        return $this
            ->contacts()
            ->update($contactId, $data);
    }

    public function mergeContacts(string $baseContactId, string $mergeContactId)
    {
        return $this
            ->contacts()
            ->merge($baseContactId, $mergeContactId);
    }

    public function searchContacts(array $data)
    {
        return $this
            ->contacts()
            ->search($data);
    }

    public function getLabels(string $contactId)
    {
        $response = $this->contacts()
            ->labels()
            ->get($contactId);

        return $response->json('payload', []);
    }

    public function updateLabels(string $contactId, array $labels)
    {
        return $this->contacts()
            ->labels()
            ->update($contactId, $labels);
    }
}
