<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_customer' => fake()->company(),
            'alamat' => fake()->address(),
            'kota' => fake()->randomElement(['Indramayu', 'Cirebon', 'Majalengka', 'Kuningan']),
            'rayon_id' => \App\Models\Rayon::factory(),
        ];
    }
}
