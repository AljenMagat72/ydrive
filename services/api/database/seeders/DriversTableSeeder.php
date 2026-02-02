<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DriversTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $cities = [
            'peterborough' => 'peterborough',
            'sudbury' => 'sudbury',
            'medicine_hat' => 'medicine_hat',
            'cobourg' => 'cobourg',
            'lindsay' => 'lindsay',
            'lethbridge' => 'lethbridge',
        ];

        foreach ($cities as $cityName => $cityId) {
            for ($i = 0; $i < 10; $i++) { // 10 users per city
                DB::table('drivers')->insert([
                    'autofleet_driver_id' => 'AF' . $faker->unique()->numberBetween(1000, 9999),
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'city_id' => $cityId,
                    'phone_number' => $faker->phoneNumber,
                    'is_active' => 1,
                    'is_delinquent' => 0,
                    'acceptance_rate' => $faker->numberBetween(50, 100), // random acceptance
                    'minimum_scheduled_hours' => $faker->numberBetween(10, 40), // random schedule
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Drivers table seeded successfully!');
    }
}
