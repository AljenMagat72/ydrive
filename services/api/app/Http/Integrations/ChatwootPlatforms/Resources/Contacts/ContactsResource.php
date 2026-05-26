<?php

namespace App\Http\Integrations\ChatwootPlatforms\Resources\Contacts;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

use App\Http\Integrations\ChatwootPlatforms\Resources\Contacts\Labels\LabelsResource;
use App\Http\Integrations\ChatwootPlatforms\Resources\Contacts\Notes\NotesResource;

use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\CreateContact;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\GetContact;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\MergeContacts;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\SearchContacts;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\UpdateContact;

class ContactsResource extends BaseResource
{
    public function __construct(Connector $connector, protected string $accountId)
    {
        parent::__construct($connector);
    }

    public function get(array $data)
    {
        return $this->connector->send(new GetContact($this->accountId, $data));
    }

    public function create(array $data)
    {
        return $this->connector->send(new CreateContact($this->accountId, $data));
    }

    public function update(string $contactId, array $data)
    {
        return $this->connector->send(new UpdateContact($this->accountId, $contactId, $data));
    }

    public function search(array $data)
    {
        return $this->connector->send(new SearchContacts($this->accountId, $data));
    }

    public function merge(string $baseContactId, string $mergeeContactId)
    {
        return $this->connector->send(new MergeContacts($this->accountId, $baseContactId, $mergeeContactId));
    }

    public function labels()
    {
        return new LabelsResource($this->connector, $this->accountId);
    }

    public function notes()
    {
        return new NotesResource($this->connector, $this->accountId);
    }
}
