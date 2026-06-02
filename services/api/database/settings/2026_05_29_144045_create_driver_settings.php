<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('driver.minimum_scheduled_hours', 60);
        $this->migrator->add('driver.minimum_acceptance_rate', 60);
    }
};
