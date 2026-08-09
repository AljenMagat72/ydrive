<?php

namespace Database\Factories;

use App\Models\Clients\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $autofleetId = $this->faker->uuid();

        return [
            'uuid' => $autofleetId,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->e164PhoneNumber,
            'email' => $this->faker->email,
            'autofleet_client_id' => $autofleetId,
            'avatar_url' => $this->faker->imageUrl,
            'is_active' => true,
        ];
    }
}
