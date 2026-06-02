<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DriverSettings extends Settings
{
    public int $minimum_scheduled_hours;
    public int $minimum_acceptance_rate;
    public static function group(): string
    {
        return 'driver';
    }
}
