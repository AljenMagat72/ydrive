<?php

namespace App\Filament\Pages;

use App\Facades\Token;
use App\Models\AdminInvite;
use App\Services\Admin\AdminUserService;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Url;

class CreateAccount extends SimplePage
{
    public static ?string $slug = 'verify';

    public static string $route = 'auth.create';

    protected string $view = 'admin::pages.create-account';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'token' => request('token'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('token'),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(Password::min(8)->max(32)->letters()->numbers())
                    ->live(onBlur: true),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->same('password')
                    ->live(onBlur: true),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->livewireSubmitHandler('verify')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->fullWidth(true),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('verify')
                ->label('Set Password')
                ->submit('verify'),
        ];
    }

    public function verify(): void
    {
        $data = $this->form->getState();

        $invite = AdminInvite::where(['token' => Token::hash($data['token'])])->firstOrFail();

        app(AdminUserService::class)->verify($invite, $data['password']);

        Notification::make()->title('Account Verified')->success()->send();

        Redirect::to(route('filament.admin.auth.login'));
    }
}
