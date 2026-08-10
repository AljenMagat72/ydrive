<?php

namespace App\Http\Integrations\GoogleSheets\Resources;

use App\Http\Integrations\GoogleSheets\Requests\AppendValues;
use App\Http\Integrations\GoogleSheets\Requests\GetValues;
use App\Http\Integrations\GoogleSheets\Requests\UpdateValues;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Saloon\Http\Response;

class ValuesResource extends BaseResource
{
    public function __construct(Connector $connector, protected string $spreadsheetId)
    {
        parent::__construct($connector);
    }

    public function get(string $range): Response
    {
        return $this->connector->send(new GetValues($this->spreadsheetId, $range));
    }

    public function append(string $range, array $values): Response
    {
        return $this->connector->send(new AppendValues($this->spreadsheetId, $range, $values));
    }

    public function update(string $range, array $values): Response
    {
        return $this->connector->send(new UpdateValues($this->spreadsheetId, $range, $values));
    }
}
