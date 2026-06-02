<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin;
use App\Services\Admin\AdminUserService;
use Illuminate\Console\Command;

class ResendAdminInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:resend {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend admin invite';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $admin = Admin::where('email', $email)->firstOrFail();

        app(AdminUserService::class)->send($admin);
    }
}
