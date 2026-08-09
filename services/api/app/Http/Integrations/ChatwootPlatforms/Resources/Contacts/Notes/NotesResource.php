<?php

namespace App\Http\Integrations\ChatwootPlatforms\Resources\Contacts\Notes;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Notes\CreateNote;

class NotesResource extends BaseResource
{
    public function __construct(Connector $connector, protected string $accountId)
    {
        parent::__construct($connector);
    }

    public function create(string $contactId, string $note)
    {
        return $this->connector->send(new CreateNote($this->accountId, $contactId, $note));
    }
}
