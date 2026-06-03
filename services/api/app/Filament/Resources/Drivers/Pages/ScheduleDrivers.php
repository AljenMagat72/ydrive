<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Resources\Drivers\DriverResource;
use App\Helpers\Cities;
use App\Models\DriverSchedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;

class ScheduleDrivers extends Page
{
    protected static string $resource = DriverResource::class;

    protected string $view = 'admin::pages.drivers.schedule-drivers';

    #[Url]
    public string $date;

    #[Url]
    public string $city = '';
    public array $schedules;

    public function mount()
    {
        $this->date = $this->date ?? Carbon::today()->toDateString();

        if (blank($this->city)) {
            $this->city = (string) array_key_first(Cities::toOptions());
        }

        $this->loadSchedules();
    }

    public function form(Schema $form)
    {
        return $form
            ->schema([
                Flex::make([
                    DatePicker::make('date')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->hiddenLabel()
                        ->afterStateUpdated(fn() => $this->loadSchedules())
                        ->live(),
                    Select::make('city')
                        ->native(false)
                        ->hiddenLabel()
                        ->selectablePlaceholder(false)
                        ->options(Cities::toOptions())
                        ->afterStateUpdated(fn() => $this->loadSchedules())
                        ->live()
                ])
                    ->maxWidth('sm')
            ]);
    }

    public function loadSchedules()
    {
        $dayStart = Carbon::parse($this->date)->startOfDay();
        $dayEnd = Carbon::parse($this->date)->endOfDay();

        $this->schedules = DriverSchedule::with('driver')
            ->when($this->city, fn($q) => $q->whereHas('driver', fn($q) => $q->where('city_id', $this->city)))
            ->where(fn($q) => $q
                ->whereDate('starts_at', $this->date)
                ->orWhereDate('ends_at', $this->date)
            )
            ->orderBy('starts_at')
            ->get()
            ->groupBy('driver_id')
            ->map(fn($shifts) => $shifts->map(function (DriverSchedule $shift) use ($dayStart, $dayEnd) {
                $start = Carbon::parse($shift->starts_at)->max($dayStart);
                $end = Carbon::parse($shift->ends_at)->min($dayEnd);

                $startPercent = $dayStart->diffInMinutes($start) / 1440 * 100;
                $endPercent = $dayStart->diffInMinutes($end) / 1440 * 100;

                return [
                    ...$shift->toArray(),
                    'left' => round($startPercent, 4),
                    'width' => round($endPercent - $startPercent, 4),
                ];
            }))
            ->toArray();
    }

    public function getTimeSlotsProperty(): array
    {
        return array_map(
            fn($i) => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
            range(0, 23)
        );
    }
}
