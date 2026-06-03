<?php

namespace App\Services\Admin;

use App\Exceptions\Admin\AlreadyUsedInviteException;
use App\Exceptions\Admin\ExpiredInviteException;
use App\Facades\Token;
use App\Filament\Pages\CreateAccount;
use App\Models\Admin;
use App\Models\AdminInvite;
use App\Notifications\InviteAdminNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class AdminUserService
{
    public function invite(string $name, string $email)
    {
        $admin = Admin::create([
            'email' => $email,
            'name' => $name,
            //  random temporary password
            'password' => Str::password(32),
        ]);

        $this->send($admin);
    }

    public function send(Admin $admin)
    {
        AdminInvite::where('admin_id', $admin->id)
            ->whereNull('accepted_at')
            ->update([
                'expires_at' => now(),
            ]);

        $token = Token::generate();

        AdminInvite::create([
            'token' => Token::hash($token),
            'admin_id' => $admin->id,
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        $url = Url::signedRoute(
            "filament.admin." . CreateAccount::$route,
            ['token' => $token],
            Carbon::now()->addDays(7)
        );
        $admin->notify(new InviteAdminNotification($url));
    }

    public function verify(AdminInvite $invite, string $password)
    {
        if ($invite->accepted_at) {
            throw new AlreadyUsedInviteException();
        }

        if ($invite->expires_at && $invite->expires_at->isPast()) {
            throw new ExpiredInviteException();
        }

        $admin = Admin::findOrFail($invite->admin_id);

        $admin->update([
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $invite->update([
            'accepted_at' => Carbon::now(),
        ]);
    }
}
