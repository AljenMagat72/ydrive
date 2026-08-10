<?php

namespace App\Http\Integrations\GHL\Resources;

use App\Http\Integrations\GHL\Requests\Contacts\CreateContact;
use App\Http\Integrations\GHL\Requests\Contacts\SearchContacts;
use App\Http\Integrations\GHL\Requests\Contacts\UpdateContact;
use App\Http\Integrations\GHL\Requests\Contacts\UpsertContact;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class ContactsResource extends BaseResource
{
    public function upsert(array $data): Response
    {
        return $this->connector->send(new UpsertContact($data));
    }

    public function create(array $data): Response
    {
        return $this->connector->send(new CreateContact($data));
    }

    public function update(string $contactId, array $data): Response
    {
        return $this->connector->send(new UpdateContact($contactId, $data));
    }

    public function search(array $query): Response
    {
        return $this->connector->send(new SearchContacts($query));
    }
}
