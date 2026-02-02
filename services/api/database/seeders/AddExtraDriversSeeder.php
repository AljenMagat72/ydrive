<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AddExtraDriversSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Define cities
        $cities = ['lindsay', 'cobourg'];

        for ($i = 0; $i < 10; $i++) {
            $city = $faker->randomElement($cities);

            DB::table('drivers')->insert([
                'autofleet_driver_id' => 'AF' . $faker->unique()->numberBetween(1000, 9999),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'city_id' => $city,
                'phone_number' => $faker->phoneNumber,
                'is_active' => 1,
                'is_delinquent' => 0,
                'acceptance_rate' => $faker->numberBetween(0, 100),
                'minimum_scheduled_hours' => $faker->randomFloat(2, 0, 40),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
