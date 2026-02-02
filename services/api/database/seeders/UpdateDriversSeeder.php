<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UpdateDriversSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get 30 random drivers
        $drivers = DB::table('drivers')->inRandomOrder()->limit(30)->pluck('id');

        foreach ($drivers as $driverId) {
            // Delete all schedules for this driver
            DB::table('driver_schedules')->where('driver_id', $driverId)->delete();

            // Optionally, reset acceptance rate or scheduled hours
            DB::table('drivers')->where('id', $driverId)->update([
                'acceptance_rate' => $faker->numberBetween(0, 100),
                'minimum_scheduled_hours' => 0,
            ]);
        }
    }
}
