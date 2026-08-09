<?php

namespace App\Http\Integrations\Zoho\Resources\Records;



use App\Http\Integrations\Zoho\Requests\Records\UpsertRecords;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

class RecordsResource extends BaseResource
{
    public function __construct(Connector $connector, protected string $api)
    {
        parent::__construct($connector);
    }

    public function upsert(array $data, array $duplicateCheckFields)
    {
        return $this->connector->send(new UpsertRecords($this->api, $data, $duplicateCheckFields));
    }
}
