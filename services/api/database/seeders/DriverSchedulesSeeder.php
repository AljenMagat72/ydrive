<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DriverSchedulesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get all drivers
        $drivers = DB::table('drivers')->pluck('id');

        // Days of the current week
        $daysOfWeek = collect(range(0, 6)); // 0=Monday, 6=Sunday

        foreach ($drivers as $driverId) {
            $totalHours = 0;
            $targetHours = $faker->numberBetween(20, 40); // total hours for the week

            // Shuffle days so random days get shifts
            $shuffledDays = $daysOfWeek->shuffle();

            foreach ($shuffledDays as $dayOffset) {
                if ($totalHours >= $targetHours) {
                    break; // stop if target hours reached
                }

                // Decide if the driver works that day (50–100% of remaining hours)
                $remainingHours = $targetHours - $totalHours;
                if ($faker->boolean(70)) { // 70% chance to work
                    $shiftLength = min($faker->numberBetween(4, 10), $remainingHours); // max shift limited by remaining hours
                    $startHour = $faker->numberBetween(6, 12); // start between 6am and noon

                    $startAt = Carbon::now()->startOfWeek()->addDays($dayOffset)->setHour($startHour)->setMinute(0)->setSecond(0);
                    $endAt = $startAt->copy()->addHours($shiftLength);

                    DB::table('driver_schedules')->insert([
                        'driver_id' => $driverId,
                        'starts_at' => $startAt,
                        'ends_at' => $endAt,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $totalHours += $shiftLength;
                }
            }
        }
    }
}
