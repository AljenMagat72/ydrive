<?php

namespace App\Http\Integrations\ChatwootPlatforms\Resources\Contacts\Labels;

use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Labels\GetLabels;
use App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Labels\UpdateLabels;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;


class LabelsResource extends BaseResource
{
    public function __construct(Connector $connector, protected string $accountId)
    {
        parent::__construct($connector);
    }

    public function update(string $contactId, array $data)
    {
        return $this->connector->send(new UpdateLabels($this->accountId, $contactId, $data));
    }

    public function get(string $contactId)
    {
        return $this->connector->send(new GetLabels($this->accountId, $contactId));
    }
}
