<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Resources\Drivers\DriverResource;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;

class DriverSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = DriverResource::class;

    protected string $view = 'admin::pages.drivers.driver-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = app(\App\Settings\DriverSettings::class);

        $this->form->fill([
            'minimum_acceptance_rate' => $settings->minimum_acceptance_rate,
            'minimum_scheduled_hours' => $settings->minimum_scheduled_hours,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('minimum_acceptance_rate')->numeric()->maxWidth('xs'),
                TextInput::make('minimum_scheduled_hours')->numeric()->maxWidth('xs'),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $settings = app(\App\Settings\DriverSettings::class);
        $state = $this->form->getState();

        $settings->minimum_acceptance_rate = $state['minimum_acceptance_rate'];
        $settings->minimum_scheduled_hours = $state['minimum_scheduled_hours'];
        $settings->save();

        Notification::make()->title('Saved')->success()->send();
    }
}
