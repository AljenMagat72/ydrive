<?php

namespace App\Console\Commands\Autofleet;

use App\Services\Autofleet\AutofleetAuthenticationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('autofleet:authenticate {refreshToken}')]
#[Description('Set the autofleet refresh token and refresh access token')]
class AutofleetAuthenticate extends Command
{
    public function __construct(protected AutofleetAuthenticationService $authenticationService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $refreshToken = $this->argument('refreshToken');
        $this->authenticationService->authenticate($refreshToken);
    }
}
