<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Hash;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:create-admin {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        Admin::create([
            'name' => fake()->name(),
            'password' => Hash::make($password),
            'email' => $email,
        ]);
    }
}
