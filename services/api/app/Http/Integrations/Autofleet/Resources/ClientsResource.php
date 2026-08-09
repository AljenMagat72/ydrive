<?php

namespace App\Http\Integrations\Autofleet\Resources;

use App\Http\Integrations\Autofleet\Requests\Clients\GetClientRequest;
use App\Http\Integrations\Autofleet\Requests\Clients\QueryClientsRequest;
use Saloon\Http\BaseResource;

class ClientsResource extends BaseResource
{
    public function get(string $id)
    {
        return $this->connector->send(new GetClientRequest($id));
    }

    public function query(string $searchTerm, int $page)
    {
        return $this->connector->send(new QueryClientsRequest($searchTerm, $page));
    }
}
