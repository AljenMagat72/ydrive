<?php

namespace App\Http\Integrations\Twilio\Resources;

use App\Http\Integrations\Twilio\Requests\Messages\SendMessage;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class MessagesResource extends BaseResource
{
    public function send(array $payload): Response
    {
        return $this->connector->send(new SendMessage($payload));
    }
}
