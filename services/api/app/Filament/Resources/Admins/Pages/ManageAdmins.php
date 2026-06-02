<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use App\Services\Admin\AdminUserService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use UnitEnum;

class ManageAdmins extends ManageRecords
{
    protected static string $resource = AdminResource::class;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return "Admins";
    }

    public static function getNavigationLabel(): string
    {
        return "List";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invite')
                ->form([
                    TextInput::make('email')->email()->required(),
                    TextInput::make('name')->required(),
                ])
                ->action(function (array $data) {
                    app(AdminUserService::class)->invite($data['name'], $data['email']);
                    Notification::make()->success()->title('Invite Sent')->send();
                })
                ->modalWidth('md'),
        ];
    }
}
