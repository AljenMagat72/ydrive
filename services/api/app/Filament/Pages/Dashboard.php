<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $slug = '/';
    protected static \BackedEnum|string|null $navigationIcon = 'lucide-car';
    protected string $view = 'admin::pages.dashboard';
}
