<?php

namespace App\Http\Integrations\Airtable\Resources;

use App\Http\Integrations\Airtable\Requests\CreateRecords;
use App\Http\Integrations\Airtable\Requests\ListRecords;
use App\Http\Integrations\Airtable\Requests\UpdateRecord;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Saloon\Http\Response;

class RecordsResource extends BaseResource
{
    public function __construct(Connector $connector, protected string $table)
    {
        parent::__construct($connector);
    }

    public function list(array $query = []): Response
    {
        return $this->connector->send(new ListRecords($this->table, $query));
    }

    public function create(array $records): Response
    {
        return $this->connector->send(new CreateRecords($this->table, $records));
    }

    public function update(string $recordId, array $fields): Response
    {
        return $this->connector->send(new UpdateRecord($this->table, $recordId, $fields));
    }
}
