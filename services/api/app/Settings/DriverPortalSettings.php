<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DriverPortalSettings extends Settings
{
    public string $motd;

    public static function group(): string
    {
        return 'driver_portal';
    }
}
