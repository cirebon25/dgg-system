<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Rayon dulu (Misal 5 Rayon)
        \App\Models\Rayon::factory(5)->create();

        // 2. Buat Teknisi (Misal 5 orang)
        \App\Models\Technician::factory(5)->create();

        // 3. Buat Master Model Mesin (Misal 10 model)
        \App\Models\MachineModel::factory(10)->create();

        // 4. Buat Sparepart (50 jenis)
        \App\Models\Sparepart::factory(50)->create();

        // 5. Buat Customer (50 data)
        \App\Models\Customer::factory(50)->create();

        // 6. Buat Mesin (50 unit)
        \App\Models\Machine::factory(50)->create();

        // 7. Buat Deployment (Pasangkan Mesin ke Customer)
        $machines = \App\Models\Machine::all();
        $customers = \App\Models\Customer::all();

        foreach ($machines as $index => $machine) {
            if (isset($customers[$index])) {
                \App\Models\Deployment::create([
                    'machine_id' => $machine->id,
                    'customer_id' => $customers[$index]->id,
                    'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
                ]);
            }
        }

        // 8. Terakhir buat Service Log (50 record)
        \App\Models\ServiceLog::factory(50)->create();
    }
}
