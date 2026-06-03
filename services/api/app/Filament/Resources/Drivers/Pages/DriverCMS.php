<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Resources\Drivers\DriverResource;
use App\Settings\DriverPortalSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;

class DriverCMS extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = DriverResource::class;

    protected string $view = 'admin::pages.drivers.driver-cms';

    public $motd = '';

    public function mount(): void
    {
        $this->motd = app(DriverPortalSettings::class)->motd ?? '';
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                RichEditor::make('motd')
            ]);
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'CMS';
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
        $settings = app(DriverPortalSettings::class);
        $settings->motd = $this->form->getState()['motd'];
        $settings->save();

        $this->getSavedNotification()?->send();
    }

    protected function getSavedNotification()
    {
        return \Filament\Notifications\Notification::make()
            ->title('Saved')
            ->success();
    }
}
