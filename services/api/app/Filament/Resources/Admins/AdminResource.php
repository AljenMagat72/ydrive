<?php

namespace App\Filament\Resources\Admins;

use App\Filament\Resources\Admins\Pages\ManageAdmins;
use App\Models\Admin;
use App\Services\Admin\AdminUserService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = 'Admins';
    protected static ?string $navigationLabel = 'List';
    protected static ?string $recordTitleAttribute = 'Admin';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Admin')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->sortable()
                    ->sortable(query: function (Builder $query, string $direction) {
                        $query->orderByRaw("email_verified_at IS NULL {$direction}");
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('Resend Invite')
                        ->icon('lucide-send')
                        ->visible(fn(Admin $record) => $record->email_verified_at === null)
                        ->action(function (Admin $record) {
                            app(AdminUserService::class)->send($record);
                            Notification::make()->success()->title('Invite Resent')->send();
                        }),

                    Action::make('Revoke Access')
                        ->icon('lucide-unlink')
                        ->color('danger')
                        ->visible(fn(Admin $record) => $record->email_verified_at !== null)
                        ->requiresConfirmation()
                        ->action(function (Admin $record) {
                            DB::table('sessions')->where('user_id', $record->id)->delete();
                            Notification::make()->success()->title('Access Revoked')->send();
                        }),

                    DeleteAction::make()->icon('lucide-trash'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAdmins::route('/'),
        ];
    }
}
