<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vehicle::create([
            'driver_id' => 4,
            'license_plate' => 'L300-001',
            'make' => 'Mitsubishi',
            'model' => 'L300',
            'year' => 2022,
            'capacity' => 20,
        ]);

        Vehicle::create([
            'driver_id' => 5,
            'license_plate' => 'L300-002',
            'make' => 'Mitsubishi',
            'model' => 'L300',
            'year' => 2023,
            'capacity' => 20,
        ]);
    }
}
