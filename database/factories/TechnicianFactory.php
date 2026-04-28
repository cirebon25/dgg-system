<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Technician>
 */
class TechnicianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'nama_technician' => fake()->name(),
        'phone' => fake()->phoneNumber(),
        // Baris di bawah ini memastikan Teknisi punya Rayon yang valid
        'rayon_id' => \App\Models\Rayon::all()->random()->id ?? \App\Models\Rayon::factory(),

        ];
}
}
