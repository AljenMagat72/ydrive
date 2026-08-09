<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Resources\Drivers\DriverResource;
use Filament\Resources\Pages\Page;

class OverviewDrivers extends Page
{
    protected static string $resource = DriverResource::class;

    protected string $view = 'admin::pages.drivers.overview-drivers';
}
