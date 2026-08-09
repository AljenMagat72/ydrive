<?php

namespace App\Filament\Resources\Drivers;

use App\Filament\Resources\Drivers\Pages\DriverCMS;
use App\Filament\Resources\Drivers\Pages\DriverSettings;
use App\Filament\Resources\Drivers\Pages\ListDrivers;
use App\Filament\Resources\Drivers\Pages\OverviewDrivers;
use App\Filament\Resources\Drivers\Pages\ScheduleDrivers;
use App\Filament\Resources\Drivers\Pages\ViewDriver;
use App\Filament\Resources\Drivers\Schemas\DriverInfolist;
use App\Filament\Resources\Drivers\Tables\DriversTable;
use App\Models\Driver;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function infolist(Schema $schema): Schema
    {
        return DriverInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DriversTable::configure($table);
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Overview')
                ->group('Drivers')
                ->icon('lucide-users')
                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.drivers.index'))
                ->url(static::getUrl('index')),

            NavigationItem::make('List')
                ->group('Drivers')
                ->icon('lucide-users')
                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.drivers.list') || request()->routeIs('filament.admin.resources.drivers.view'))
                ->url(static::getUrl('list')),

            NavigationItem::make('Schedule')
                ->group('Drivers')
                ->icon('lucide-calendar')
                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.drivers.schedule'))
                ->url(static::getUrl('schedule')),

            NavigationItem::make('CMS')
                ->group('Drivers')
                ->icon('lucide-notepad-text')
                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.drivers.cms'))
                ->url(static::getUrl('cms')),

            NavigationItem::make('Settings')
                ->group('Drivers')
                ->icon('lucide-cog')
                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.drivers.settings'))
                ->url(static::getUrl('settings')),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => OverviewDrivers::route('/'),
            'list' => ListDrivers::route('/list'),
            'schedule' => ScheduleDrivers::route('/schedule'),
            'cms' => DriverCMS::route('/cms'),
            'settings' => DriverSettings::route('/settings'),
            'view' => ViewDriver::route('/{record}'),
        ];
    }
}
