<?php

namespace App\Http\Integrations\Twilio;

use App\Http\Integrations\Twilio\Resources\MessagesResource;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class TwilioApi extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        $accountSid = config('services.twilio.account_sid', 'ACmockaccountsid');

        return "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}";
    }

    protected function defaultAuth(): Authenticator
    {
        return new BasicAuthenticator(
            config('services.twilio.account_sid', 'ACmockaccountsid'),
            config('services.twilio.auth_token', 'mock-twilio-auth-token'),
        );
    }

    public function messages(): MessagesResource
    {
        return new MessagesResource($this);
    }
}
