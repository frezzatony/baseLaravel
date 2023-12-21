<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'social_name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'login' => "04853773908",
            'password' => '$2y$10$o7Jg56/5uSTRlCX3cwWUseL11bUHyqVuQdpiZswkR/g5b2P1PWCfi',

        ];
    }
}
