<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = \App\Models\Driver::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'autofleet_driver_id' => $this->faker->uuid(),
            'city_id' => $this->faker->randomElement(['Peterborough', 'Medicine Hat', 'Oshawa']),
        ];
    }
}
