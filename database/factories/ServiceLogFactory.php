<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceLog>
 */
class ServiceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'tanggal' => fake()->dateTimeBetween('-2 months', 'now'),
        'machine_id' => \App\Models\Machine::all()->random()->id,
        'technician_id' => \App\Models\Technician::all()->random()->id,
        'kerusakan' => fake()->randomElement(['E000', 'Paper Jam', 'Hasil Kotor', 'Mati Total']),
        'perbaikan' => fake()->randomElement(['Ganti Drum', 'Cleaning Corona', 'Tukar Guling Unit']),
    ];
}
}
