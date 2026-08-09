<?php

namespace App\Console\Commands\Admin;

use App\Services\Admin\AdminUserService;

use Illuminate\Console\Command;

class InviteAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:invite {email} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invite admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');

        app(AdminUserService::class)->invite($name, $email);
    }
}
