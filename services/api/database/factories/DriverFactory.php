<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = \App\Models\Driver::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,   // snake_case
            'last_name' => $this->faker->lastName,     // snake_case
            'city' => $this->faker->randomElement(['Manila', 'Cebu', 'Davao']),
            'has_current_schedule' => $this->faker->boolean,
            'acceptance' => $this->faker->numberBetween(30, 100),
            'rejected' => $this->faker->numberBetween(0, 10),
            'ignored' => $this->faker->numberBetween(0, 5),
            'enabled' => $this->faker->boolean,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ];
    }
}
