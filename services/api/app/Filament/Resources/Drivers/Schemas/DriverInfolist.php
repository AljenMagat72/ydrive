<?php

namespace App\Filament\Resources\Drivers\Schemas;

use App\Models\Driver;
use App\Models\DriverSchedule;
use App\Settings\DriverSettings;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Carbon;

class DriverInfolist
{

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    #region external
                    Section::make('External Identifiers')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('autofleet_driver_id')
                                ->label('Autofleet ID')
                                ->url(
                                    fn($record) => "https://control.autofleet.io/6FnkvuL1DSM3pe847fDhCX/fleet/drivers/{$record->autofleet_driver_id}"
                                )
                                ->placeholder('-')
                                ->openUrlInNewTab()
                                ->color('primary'),

                            TextEntry::make('zoho_id')
                                ->label('Zoho CRM ID')
                                ->placeholder('-')
                                ->url(
                                    fn($record) => "https://crm.zoho.com/crm/org12345678/tab/Potentials/{$record->zoho_id}/canvas"
                                )
                                ->placeholder('-')
                                ->openUrlInNewTab()
                                ->color('primary'),
                        ])
                        ->headerActions([
                            EditAction::make('Edit Identifiers')
                                ->label('Edit')
                                ->form([
                                    TextInput::make('autofleet_driver_id')
                                        ->label('Autofleet Id'),
                                    TextInput::make('zoho_id')
                                        ->label('Zoho Id'),
                                ])
                                ->modalWidth(Width::Medium),
                        ]),
                    #endregion
                    #region details
                    Section::make('Details')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('first_name'),
                            TextEntry::make('last_name'),
                            TextEntry::make('phone_number')
                                ->url(fn($record) => "tel:{$record->phone_number}")
                                ->color('primary')
                                ->placeholder('-'),
                            TextEntry::make('city_id')->label('City ID'),
                        ]),
                    #endregion
                    #region status
                    Section::make('Status')
                        ->columns(3)
                        ->schema([
                            IconEntry::make('is_active')->boolean(),
                            IconEntry::make('is_delinquent')->boolean(),
                            TextEntry::make('acceptance_rate')
                                ->numeric()
                                ->suffix('%')
                                ->color(fn($state, $record) => $state < $record->resolved_minimum_acceptance_rate ? 'danger' : 'success'),
                            TextEntry::make('minimum_scheduled_hours')
                                ->label('Minimum Scheduled Hours')
                                ->numeric()
                                ->suffix(' Hrs')
                                ->placeholder(app(DriverSettings::class)->minimum_scheduled_hours." Hrs"),
                            TextEntry::make('minimum_acceptance_rate')
                                ->label('Minimum Acceptance Rate')
                                ->numeric()
                                ->suffix('%')
                                ->placeholder(app(DriverSettings::class)->minimum_acceptance_rate."%"),
                        ])
                        ->headerActions([
                            EditAction::make('Edit Status')
                                ->label('Edit')
                                ->schema([
                                    TextInput::make('minimum_scheduled_hours')
                                        ->label('Minimum Scheduled Hours')
                                        ->placeholder(app(DriverSettings::class)->minimum_scheduled_hours)
                                        ->numeric(),
                                    TextInput::make('minimum_acceptance_rate')
                                        ->label('Minimum Acceptance Rate')
                                        ->placeholder(app(DriverSettings::class)->minimum_acceptance_rate)
                                        ->numeric(),
                                ])
                                ->modalWidth(Width::Medium),
                        ]),
                    #endregion
                    #region documents
                    Section::make('Documents')
                        ->columns(3)
                        ->schema([

                        ]),
                    #endregion
                ]),
                #region schedule
                Section::make(function ($livewire) {
                    return 'Week of ' . Carbon::parse($livewire->activeWeek)->format('M j');
                })
                    ->headerActions([
                        Action::make('prev_week')
                            ->hiddenLabel()
                            ->icon('lucide-chevron-left')
                            ->action(function ($livewire) {
                                $livewire->activeWeek = Carbon::parse($livewire->activeWeek)->subWeek()->format('Y-m-d');
                            }),
                        Action::make('next_week')
                            ->hiddenLabel()
                            ->icon('lucide-chevron-right')
                            ->action(function ($livewire) {
                                $livewire->activeWeek = Carbon::parse($livewire->activeWeek)->addWeek()->format('Y-m-d');
                            })
                    ])
                    ->schema([
                        TextEntry::make('total_hours')
                            ->hiddenLabel()
                            ->state(
                                fn($record, $livewire) => $record->schedules()
                                    ->whereBetween('starts_at', [
                                        Carbon::parse($livewire->activeWeek)->startOfWeek(),
                                        Carbon::parse($livewire->activeWeek)->startOfWeek()->endOfWeek(),
                                    ])
                                    ->get()
                                    ->sum(fn($s) => Carbon::parse($s->starts_at)->diffInMinutes(Carbon::parse($s->ends_at))) / 60
                            )
                            ->formatStateUsing(fn($state) => "Total: {$state}h"),
                        RepeatableEntry::make('weekly_calendar')
                            ->hiddenLabel()
                            ->contained(false)
                            ->state(function ($record, $livewire) {
                                $startOfWeek = Carbon::parse($livewire->activeWeek)->startOfWeek();
                                $days = [];

                                $schedules = $record->schedules()
                                    ->whereBetween('starts_at', [$startOfWeek, $startOfWeek->copy()->endOfWeek()])
                                    ->orderBy('starts_at')
                                    ->get()
                                    ->groupBy(fn($s) => Carbon::parse($s->starts_at)->format('Y-m-d'));

                                for ($i = 0; $i < 7; $i++) {
                                    $date = $startOfWeek->copy()->addDays($i);
                                    $dateKey = $date->format('Y-m-d');

                                    $shifts = $schedules->get($dateKey) ?? collect();

                                    $minutes = $shifts->sum(
                                        fn($shift) => Carbon::parse($shift->starts_at)->diffInMinutes(Carbon::parse($shift->ends_at))
                                    );

                                    $days[] = [
                                        'date' => $date,
                                        'shifts' => $shifts,
                                        'hours' => $minutes / 60,
                                    ];
                                }
                                return $days;
                            })
                            ->schema([
                                Flex::make([
                                    TextEntry::make('date')
                                        ->hiddenLabel()
                                        ->weight('bold')
                                        ->formatStateUsing(function ($state) {
                                            return $state->format('D M j');
                                        })
                                        ->color(function ($state) {
                                            return $state->isToday() ? 'primary' : null;
                                        })
                                        ->grow(),
                                    TextEntry::make('hours')
                                        ->hiddenLabel()
                                        ->weight('bold')
                                        ->formatStateUsing(function ($state) {
                                            return "{$state}h";
                                        })
                                        ->alignEnd(),
                                ]),

                                RepeatableEntry::make('shifts')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->placeholder('No Shifts')
                                    ->schema([
                                        TextEntry::make('starts_at')
                                            ->hiddenLabel()
                                            ->formatStateUsing(function ($data, TextEntry $component) {
                                                $item = $component->getModelInstance();
                                                return Carbon::parse($item['starts_at'])->format('H:i') . ' - ' . Carbon::parse($item['ends_at'])->format('H:i');
                                            })
                                            ->suffixActions([
                                                Action::make('delete')
                                                    ->requiresConfirmation()
                                                    ->icon('lucide-x')
                                                    ->color('danger')
                                                    ->action(function ($component) {
                                                        $component->getModelInstance()->delete();
                                                    }),
                                            ]),
                                    ]),

                                Action::make('add_shift')
                                    ->label('Add')
                                    ->icon('lucide-plus')
                                    ->form([
                                        Select::make('start')
                                            ->required()
                                            ->native(false)
                                            ->options(collect(range(0, 47))->mapWithKeys(function ($i) {
                                                $time = Carbon::today()->addMinutes($i * 30)->format('H:i');
                                                return [$time => $time];
                                            })),
                                        Select::make('end')
                                            ->required()
                                            ->native(false)
                                            ->options(collect(range(0, 47))->mapWithKeys(function ($i) {
                                                $time = Carbon::today()->addMinutes($i * 30)->format('H:i');
                                                return [$time => $time];
                                            })),
                                    ])
                                    ->action(function (array $data, Action $action, $state, Driver $record) {
                                        $index = str($action->getSchemaContainer()->getStatePath())->afterLast('.')->toInteger();

                                        $date = Carbon::parse($state[$index]['date'])->format('Y-m-d');

                                        $startsAt = Carbon::parse("$date " . $data['start']);
                                        $endsAt = Carbon::parse("$date " . $data['end']);

                                        if ($endsAt->lessThan($startsAt)) {
                                            $endsAt->addDay();
                                        }

                                        $record->schedules()->create([
                                            'starts_at' => $startsAt,
                                            'ends_at' => $endsAt,
                                        ]);
                                    })
                                    ->modalWidth('md')
                            ]),
                    ]),
                #endregion
            ]);
    }
}
