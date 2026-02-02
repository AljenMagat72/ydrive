<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateDriverAcceptanceRate extends Command
{
    protected $signature = 'driver:update-acceptance-rate';

    protected $description = 'Update driver acceptance rate from external API';

    public function handle()
    {
        $response = Http::get(config('autofleet.scrapper_url'));

        if ($response->failed()) {
            $this->error('API request failed.');

            return Command::FAILURE;
        }

        $driversData = $response->json();

        DB::transaction(function () use ($driversData, &$updatedCount) {
            $updatedCount = 0;

            foreach ($driversData as $driver) {
                $updated = Driver::where('autofleet_driver_id', $driver['id'])
                    ->update([
                        'acceptance_rate'  => round($driver['acceptance_rate']),
                        'rejected_offers'  => $driver['rejected_offers'] ?? 0,
                        'expired_offers'   => $driver['expired_offers'] ?? 0,
                    ]);

                if ($updated) {
                    $updatedCount++;
                }
            }
        });
        $this->info("Acceptance rate for {$updatedCount} drivers updated successfully.");

        return Command::SUCCESS;
    }
}
