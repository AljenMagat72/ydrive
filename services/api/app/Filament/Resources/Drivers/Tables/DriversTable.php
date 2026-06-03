<?php

namespace App\Filament\Resources\Drivers\Tables;

use App\Helpers\Cities;
use App\Models\Driver;
use App\Services\Driver\DriverDelinquentService;
use App\Settings\DriverSettings;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Flex;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function () {
                $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                $defaultHours = app(DriverSettings::class)->minimum_scheduled_hours;

                return Driver::query()->selectRaw('drivers.*, (
                    SELECT COALESCE(SUM(EXTRACT(EPOCH FROM (ends_at - starts_at)) / 3600), 0)
                    FROM driver_schedules
                    WHERE driver_schedules.driver_id = drivers.id
                    AND starts_at BETWEEN ? AND ?
                    AND deleted_at IS NULL
                    ) >= COALESCE(minimum_scheduled_hours, ?) AS has_scheduled_hours', [
                    $startOfWeek,
                    $endOfWeek,
                    $defaultHours,
                ]);
            })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('city_id')
                    ->label('City'),
                IconColumn::make('has_scheduled_hours')
                    ->boolean()
                    ->trueIcon('lucide-check')
                    ->falseIcon('lucide-x')
                    ->alignCenter(),
                TextColumn::make('rejected_offers')
                    ->numeric()
                    ->default('-')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('expired_offers')
                    ->numeric()
                    ->default('-')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('acceptance_rate')
                    ->numeric()
                    ->default('-')
                    ->alignCenter()
                    ->suffix('%')
                    ->color(fn($state, $record) => $state < $record->resolved_minimum_acceptance_rate ? 'danger' : 'success')
                    ->sortable(),
                ToggleColumn::make('is_delinquent')
                    ->label('')
                    ->disabled(fn($record) =>
                        !$record->vendor ||
                        !$record->vendor->no_opps_id
                    )
                    ->tooltip(function($record) {
                        if (!$record->vendor) {
                            return 'This record does not have a Vendor assigned.';
                        }

                        if (!$record->vendor->no_opps_id) {
                            return 'The assigned Vendor is missing a NO Opps ID.';
                        }

                        return null;
                    })
                    ->state(fn($record) => !$record->is_delinquent)
                    ->updateStateUsing(function ($record, $state) {
                        try {
                            if ($state) {
                                app(DriverDelinquentService::class)->unflag($record);
                            } else {
                                app(DriverDelinquentService::class)->flag($record);
                            }
                        } catch (\Exception $e) {
                            Notification::make('error')
                                ->title('Action Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('City')
                    ->options(
                        Cities::toOptions(),
                    )
                    ->placeholder('All'),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->default('Active')
                    ->queries(
                        true: fn(Builder $query) => $query->where('is_active', true),
                        false: fn(Builder $query) => $query->where('is_active', false),
                        blank: fn(Builder $query) => $query,
                    ),
                Filter::make('acceptance_rate_range')
                    ->schema([
                        Flex::make([
                            TextInput::make('min')
                                ->minValue(0)
                                ->maxValue(100)
                                ->placeholder(0)
                                ->nullable()
                                ->numeric()
                                ->live(debounce: 500),
                            TextInput::make('max')
                                ->minValue(0)
                                ->maxValue(100)
                                ->placeholder(100)
                                ->nullable()
                                ->numeric()
                                ->live(debounce: 500),
                        ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min'], fn($q, $val) => $q->where('acceptance_rate', '>=', $val))
                            ->when($data['max'], fn($q, $val) => $q->where('acceptance_rate', '<=', $val));
                    }),
                Filter::make('name')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->placeholder('Driver Name')
                            ->live(debounce: 500)
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['name'],
                            fn($query, $value) => $query->where(function ($query) use ($value) {
                                $query->whereLike('first_name', "%{$value}%")
                                    ->orWhereLike('last_name', "%{$value}%");
                            }),
                        );
                    })
            ], layout: FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->hiddenFilterIndicators(true)
            ->recordActions([
                EditAction::make('edit')
                    ->hiddenLabel(true)
                    ->icon('lucide-wrench')
                    ->extraAttributes(['class' => 'opacity-0 group-hover:opacity-100 transition-opacity'])
                    ->form([
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
            ], position: RecordActionsPosition::BeforeColumns)
            ->recordClasses(['group']);
    }
}
