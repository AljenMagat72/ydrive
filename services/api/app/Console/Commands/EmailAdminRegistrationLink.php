<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminRegistrationLinkMail;
use App\Models\RegistrationToken;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class EmailAdminRegistrationLink extends Command
{
    protected $signature = 'email:admin-link {email}';
    protected $description = 'Generate an admin registration link and email it';

    public function handle()
    {
        $email = $this->argument('email');

        $token = Str::random(60);

        RegistrationToken::create([
            'token' => $token,
            'role' => 'admin',
            'created_at' => now(),
        ]);

        $frontendUrl = env('FRONTEND_URL', 'https://admin.ydriveapp.com');
        $url = "$frontendUrl/register?token=$token&email=$email";

        Mail::to($email)->send(new AdminRegistrationLinkMail($url));

        $this->info("Admin registration link emailed to {$email}");

        return SymfonyCommand::SUCCESS;
    }
}
