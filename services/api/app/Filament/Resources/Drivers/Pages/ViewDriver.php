<?php

namespace App\Filament\Resources\Drivers\Pages;

use AlizHarb\ActivityLog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\Drivers\DriverResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDriver extends ViewRecord
{
    protected static string $resource = DriverResource::class;

    public string $activeWeek = '';

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->activeWeek = now()->startOfWeek()->format('Y-m-d');
    }

    public function getHeaderActions(): array
    {
        return [
            ActivityLogTimelineTableAction::make('Activities')
        ];
    }
}
